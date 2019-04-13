<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

/**
 * Class AuthenticateTest
 *
 * @package Tests\Middleware
 */
class AuthenticateTest extends TestCase
{
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
        $this->url = '/';
    }

    /** @test */
    public function accessWithoutAuthorizationParameter()
    {
        $this->get($this->url)
            ->assertResponseStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function accessWithoutBearerToken()
    {
        $this->get($this->url, ['Authorization' => '00000'])
            ->assertResponseStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function accessWithWrongToken()
    {
        $this->get($this->url, ['Authorization' => 'Bearer 00000'])
            ->assertResponseStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function accessWithGoodToken()
    {
        $user = factory(User::class)->create();

        $this->get($this->url, ['Authorization' => 'Bearer ' . $user->api_token])
            ->assertResponseStatus(JsonResponse::HTTP_OK);
    }
}
