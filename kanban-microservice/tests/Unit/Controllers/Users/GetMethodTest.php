<?php

namespace Tests\Unit\Controllers\Users;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * Class GetMethodTest
 *
 * @package Tests\Unit\Controllers\Users
 */
class GetMethodTest extends TestCase
{
    /** @test */
    public function resource_successfully_found()
    {
        $id = 100;
        $user = factory(User::class)->make();

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('find')->once()
            ->withArgs([$id])
            ->andReturn($user);

        $controller = new UserController($userRepository);
        $response = $controller->get($id);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($user->toJson(), json_encode($response->getData()));
    }

    /** @test */
    public function throws_an_exception_because_a_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = 100;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('find')->once()
            ->withArgs([$id])
            ->andThrow(new ModelNotFoundException('Resource not found.', JsonResponse::HTTP_NOT_FOUND));

        $controller = new UserController($userRepository);
        $controller->get($id);
    }
}
