<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 *
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * @param Request $request
     * @param Exception $e
     * @return JsonResponse
     */
    public function render($request, Exception $e): JsonResponse
    {
        if ($e instanceof ModelNotFoundException) {
            $data = ['message' => 'Resource not found.'];
            $status = JsonResponse::HTTP_NOT_FOUND;

        } elseif ($e instanceof NotFoundHttpException) {
            $data = ['message' => 'Invalid url.'];
            $status = JsonResponse::HTTP_NOT_FOUND;

        } elseif ($e instanceof AuthorizationException) {
            $data = ['message' => $e->getMessage()];
            $status = JsonResponse::HTTP_FORBIDDEN;

        } elseif ($e instanceof HttpException) {
            $data = ['message' => $e->getMessage()];
            $status = $e->getStatusCode();

        } elseif ($e instanceof AppException) {
            $data = ['message' => $e->getMessage()];
            $status = $e->getCode();
            if ($errors = $e->getErrors()) {
                $data['errors'] = $errors;
            }

        } elseif ($e instanceof ValidationException) {
            $data = [
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ];
            $status = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

        } else {
            $data = ['message' => $e->getMessage()];
            $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        if (config('app.debug')) {
            $data['debug'] = [
                'exception' => get_class($e),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'trace'     => explode("\n", $e->getTraceAsString()),
            ];
        }

        return response()->json($data, $status, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
