<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Gate::define('checkout', fn ($user) => $user->role === 'user');
Gate::define('confirm-orders', fn ($user) => $user->role === 'staff');
Gate::define('view-sales', fn ($user) => $user->role === 'admin');
    }
}
