<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

/**
 * Class FirewallTest
 *
 * @package Tests\Feature\Middleware
 */
class FirewallTest extends TestCase
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
    public function rejectWrongRequestIpAddress()
    {
        app()->config->set("app.whitelisted_ips", "0.0.0.0");
        $user = factory(User::class)->create();

        $this->get($this->url, ['Authorization' => 'Bearer ' . $user->api_token])
            ->assertResponseStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    /** @test */
    public function allowCorrectIpAddress()
    {
        app()->config->set("app.whitelisted_ips", "127.0.0.1");
        $user = factory(User::class)->create();

        $this->get($this->url, ['Authorization' => 'Bearer ' . $user->api_token])
            ->assertResponseStatus(JsonResponse::HTTP_OK);
    }

    /** @test */
    public function allowAllIpAddressesIfEmptyWhitelist()
    {
        app()->config->set("app.whitelisted_ips", "");
        $user = factory(User::class)->create();

        $this->get($this->url, ['Authorization' => 'Bearer ' . $user->api_token])
            ->assertResponseStatus(JsonResponse::HTTP_OK);
    }
}
