<?php

namespace Tests\Unit\Middleware;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

/**
 * Class AuthenticateTest
 *
 * @package Tests\Unit\Middleware
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
    public function access_without_header_params()
    {
        $this->get($this->url)
            ->assertResponseStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function access_without_bearer_token()
    {
        $this->get($this->url, ['Authorization' => '00000'])
            ->assertResponseStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function access_with_wrong_token()
    {
        $this->get($this->url, ['Authorization' => 'Bearer 00000'])
            ->assertResponseStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function access_with_good_token()
    {
        $user = factory(User::class)->create();

        $this->get($this->url, ['Authorization' => 'Bearer ' . $user->api_token])
            ->assertResponseStatus(JsonResponse::HTTP_OK);
    }
}
