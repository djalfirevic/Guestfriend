<?php

namespace App\Providers;

use App\Contracts\FindByApiTokenRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Class AuthServiceProvider
 *
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot(): void
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an Feature token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($header = $request->header('Authorization')) {
                $apiToken = preg_replace('/^Bearer /', '', $header);
                if ($auth = app(FindByApiTokenRepositoryInterface::class)->findByApiToken($apiToken)) {
                    return $auth;
                }
            }
            return null;
        });
    }
}
