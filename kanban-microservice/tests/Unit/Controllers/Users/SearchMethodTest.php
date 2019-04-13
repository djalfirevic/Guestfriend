<?php

namespace Tests\Unit\Controllers\Users;

use App\Contracts\UserRepositoryInterface;
use App\Exceptions\AppException;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class SearchMethodTest
 *
 * @package Tests\Unit\Controllers\Users
 */
class SearchMethodTest extends TestCase
{
    /**
     * @test
     * @throws AppException
     */
    public function results_successfully_found()
    {
        $users = factory(User::class, 10)->make();
        $data = [
            'query'    => 'a',
            'filter'   => ['name' => 'user', 'email' => 'com'],
            'order_by' => 'email',
            'sorting'  => 'asc',
            'page'     => 3,
            'limit'    => 3,
        ];

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('search')->once()
            ->withArgs([$data])
            ->andReturn($users);

        $request = new Request();
        $request->merge($data);

        $controller = new UserController($userRepository);
        $response = $controller->search($request);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($users->toJson(), json_encode($response->getData()));
    }

    /**
     * @test
     * @throws AppException
     */
    public function throws_an_exception_because_of_invalid_input_params()
    {
        $this->expectException(AppException::class);

        $data = [
            'query'    => 'a',
            'filter'   => ['name' => 'user', 'email' => 'com'],
            'order_by' => 'email',
            'sorting'  => 'asc',
            'page'     => 3,
            'limit'    => 3,
        ];

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('search')->once()
            ->withArgs([$data])
            ->andThrow(new AppException('Invalid input.', JsonResponse::HTTP_NOT_FOUND));

        $request = new Request();
        $request->merge($data);

        $controller = new UserController($userRepository);
        $controller->search($request);
    }
}
