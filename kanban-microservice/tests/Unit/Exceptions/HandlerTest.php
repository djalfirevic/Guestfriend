<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\AppException;
use App\Exceptions\Handler;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

/**
 * Class HandlerTest
 *
 * @package Tests\Unit\Exceptions
 */
class HandlerTest extends TestCase
{
    /**
     * @var Handler
     */
    protected $handler;

    /**
     * @var Request
     */
    protected $request;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->handler = new Handler();
        $this->request = Request::create(null);
    }

    /** @test */
    public function handle_authorization_exception()
    {
        $message = 'authorization exception';
        $exception = new AuthorizationException($message);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
    }

    /** @test */
    public function handle_app_exception_with_errors()
    {
        $message = 'app exception';
        $errors = ['custCode' => ['Invalid format.']];
        $expectedErrors = new StdClass();
        $expectedErrors->custCode = ['Invalid format.'];
        $exception = new AppException($message, JsonResponse::HTTP_BAD_REQUEST, $errors);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
        $this->assertEquals($expectedErrors, $res->getData()->errors);
    }

    /** @test */
    public function handle_app_exception_without_errors()
    {
        $message = 'app exception';
        $exception = new AppException($message, JsonResponse::HTTP_BAD_REQUEST);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
        $this->assertObjectNotHasAttribute('errors', $res->getData());
    }

    /** @test */
    public function handle_app_exception_without_status_code()
    {
        $message = 'app exception';
        $exception = new AppException($message);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
        $this->assertObjectNotHasAttribute('errors', $res->getData());
    }

    /** @test */
    public function handle_http_exception()
    {
        $message = 'http exception';
        $exception = new HttpException(JsonResponse::HTTP_BAD_REQUEST, $message);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
    }

    /** @test */
    public function handle_not_found_exception()
    {
        $message = 'Invalid url.';
        $exception = new NotFoundHttpException($message);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
    }

    /** @test */
    public function handle_model_not_found_exception()
    {
        $message = 'Resource not found.';
        $exception = new ModelNotFoundException($message);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
    }

    /** @test */
    public function handle_validation_exception()
    {
        $message = 'The given data was invalid.';
        $exception = ValidationException::withMessages(['errors' => []]);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
        $this->assertObjectHasAttribute('errors', $res->getData());
    }

    /** @test */
    public function handle_generic_exception()
    {
        $message = 'generic exception';
        $exception = new Exception($message, JsonResponse::HTTP_BAD_REQUEST);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
    }

    /** @test */
    public function show_debug_param_in_response_if_enabled()
    {
        app()->config->set("app.debug", true);

        $message = 'generic exception';
        $exception = new Exception($message, JsonResponse::HTTP_BAD_REQUEST);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
        $this->assertObjectHasAttribute('debug', $res->getData());
    }

    /** @test */
    public function dont_show_debug_param_in_response_if_disabled()
    {
        app()->config->set("app.debug", false);

        $message = 'generic exception';
        $exception = new Exception($message, JsonResponse::HTTP_BAD_REQUEST);

        $res = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $res);
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $res->getStatusCode());
        $this->assertEquals($message, $res->getData()->message);
        $this->assertObjectNotHasAttribute('debug', $res->getData());
    }
}
