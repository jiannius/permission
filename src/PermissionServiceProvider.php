<?php

namespace Jiannius\Permission;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Jiannius\Permission\Policy;

class PermissionServiceProvider extends ServiceProvider
{
    // register
    public function register() : void
    {
        //
    }

    // boot
    public function boot() : void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Gate::define('role', [Policy::class, 'role']);
        Gate::define('perm', [Policy::class, 'permission']);
        Gate::define('permission', [Policy::class, 'permission']);

        Blade::if('role', fn ($role) => auth()->user()->isRole($role));
        Blade::if('notrole', fn ($role) => !auth()->user()->isRole($role));

        Blade::if('permitted', fn ($permission) => auth()->user()->isPermitted($permission));
        Blade::if('forbidden', fn ($permission) => !auth()->user()->isForbidden($permission));
    }
}