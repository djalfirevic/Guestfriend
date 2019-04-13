<?php

namespace Tests\Unit\Controllers\Users;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\UserController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * Class DeleteMethodTest
 *
 * @package Tests\Unit\Controllers\Users
 */
class DeleteMethodTest extends TestCase
{
    /** @test */
    public function resource_successfully_deleted()
    {
        $id = 100;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('delete')->once()
            ->withArgs([$id])
            ->andReturn(true);

        $controller = new UserController($userRepository);
        $response = $controller->delete($id);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEquals([], $response->getData());
    }

    /** @test */
    public function throws_an_exception_because_a_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = 10000;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('delete')->once()
            ->withArgs([$id])
            ->andThrow(new ModelNotFoundException('Resource not found.', JsonResponse::HTTP_NOT_FOUND));

        $controller = new UserController($userRepository);
        $controller->delete($id);
    }
}
