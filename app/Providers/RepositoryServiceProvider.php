<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use App\Repositories\TimeTrackerRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\TimeTrackerRepositoryInterface;
use App\Services\Admin\DashboardService;
use App\Services\Admin\UserService;
use App\Services\Admin\AdminTimeTrackerService;
use App\Services\TimeTrackerService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(UserRepository::class, function ($app) {
            return new UserRepository($app->make(\App\Models\User::class));
        });

        $this->app->singleton(TimeTrackerRepositoryInterface::class, TimeTrackerRepository::class);
        $this->app->singleton(TimeTrackerRepository::class, function ($app) {
            return new TimeTrackerRepository($app->make(\App\Models\TimeTracker::class));
        });

        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService($app->make(UserRepositoryInterface::class));
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });

        $this->app->singleton(TimeTrackerService::class, function ($app) {
            return new TimeTrackerService(
                $app->make(TimeTrackerRepositoryInterface::class),
                $app->make(UserRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
