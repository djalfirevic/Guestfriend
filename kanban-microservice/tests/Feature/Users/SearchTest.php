<?php

namespace Tests\Feature\Users;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Mockery;
use Tests\TestCase;

/**
 * Class SearchTest
 *
 * @package Tests\Feature\Users
 */
class SearchTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var string
     */
    protected $url;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->url = '/users';
    }

    /** @test */
    public function validCase()
    {
        $users = factory(User::class, 10)->make();

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('search')->times(1)->andReturn($users);
        app()->bind(UserRepositoryInterface::class, function () use ($userRepository) {
            return $userRepository;
        });

        $this->json('GET', $this->url);

        $this->assertResponseStatus(JsonResponse::HTTP_OK);
    }
}
