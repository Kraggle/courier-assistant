<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Info;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void {
        Gate::define('view-vehicle', function (User $user, Vehicle $vehicle) {
            return $user->id == $vehicle->user_id;
        });

        Gate::define('is-ready', function (User $user) {
            return $user->hasDSP();
        });

        Gate::define('delete-info', function (User $user, Info $info) {
            return $user->id == 1 || $user->name == $info->creator;
        });

        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
