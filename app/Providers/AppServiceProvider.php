<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $permission = Permission::with('role')->get();

        //Super Admin
        Gate::before(function (User $user) {
            return $user->role_id == 1 ? true : null;
        });

        //Gate Check
        foreach($permission as $p){
            Gate::define($p->slug, function(User $user) use ($p) {
                return in_array($user->role_id,$p->role()->pluck('role_id')->toArray());
            });
        }

    }
}
