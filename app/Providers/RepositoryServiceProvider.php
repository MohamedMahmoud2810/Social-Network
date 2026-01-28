<?php

namespace App\Providers;

use App\Application\Contracts\FriendshipRepositoryInterface;
use App\Application\Contracts\PostRepositoryInterface;
use App\Application\Contracts\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentFriendshipRepository;
use App\Infrastructure\Repositories\EloquentPostRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        UserRepositoryInterface::class => EloquentUserRepository::class,
        PostRepositoryInterface::class => EloquentPostRepository::class,
        FriendshipRepositoryInterface::class => EloquentFriendshipRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
