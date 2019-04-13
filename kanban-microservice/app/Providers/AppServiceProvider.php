<?php

namespace App\Providers;

use App\Contracts\FindByApiTokenRepositoryInterface;
use App\Contracts\FindTicketHistoryRepositoryInterface;
use App\Contracts\LaneRepositoryInterface;
use App\Contracts\StoreTicketLogRepositoryInterface;
use App\Contracts\TicketLogRepositoryInterface;
use App\Contracts\TicketRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use App\Observers\TicketObserver;
use App\Observers\UserObserver;
use App\Repositories\LaneRepository;
use App\Repositories\TicketLogRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use App\Transformers\TicketLogTransformer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

/**
 * Class AppServiceProvider
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->repositories();
        $this->services();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        User::observe(UserObserver::class);
        Ticket::observe(TicketObserver::class);
    }

    /**
     *
     */
    private function repositories(): void
    {
        $this->app->bind(FindByApiTokenRepositoryInterface::class, function () {
            return new UserRepository(
                $this->app->make(User::class)
            );
        });

        $this->app->bind(FindTicketHistoryRepositoryInterface::class, function () {
            return new TicketLogRepository(
                $this->app->make(TicketLog::class)
            );
        });

        $this->app->bind(LaneRepositoryInterface::class, function () {
            return new LaneRepository(
                $this->app->make(Lane::class)
            );
        });

        $this->app->bind(StoreTicketLogRepositoryInterface::class, function () {
            return new TicketLogRepository(
                $this->app->make(TicketLog::class)
            );
        });

        $this->app->bind(TicketRepositoryInterface::class, function () {
            return new TicketRepository(
                $this->app->make(Ticket::class)
            );
        });

        $this->app->bind(UserRepositoryInterface::class, function () {
            return new UserRepository(
                $this->app->make(User::class)
            );
        });
    }

    /**
     *
     */
    private function services(): void
    {
        $this->app->bind(Manager::class, function () {
            return (new Manager())->setSerializer(new ArraySerializer());
        });

        $this->app->bind(TicketLogTransformer::class, function () {
            return new TicketLogTransformer(
                $this->app->make(UserRepositoryInterface::class),
                $this->app->make(LaneRepositoryInterface::class)
            );
        });
    }
}
