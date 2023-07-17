<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
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
    public function boot(): void
    {
        Passport::tokensCan(config('passport.scope_list'));
        Passport::setDefaultScope(config('passport.default_scope')); 

        Route::group(['as' => 'passport.','prefix' => config('passport.path', 'oauth'),'namespace' => 'Laravel\Passport\Http\Controllers'], function () {
            $this->loadRoutesFrom(__DIR__.'/../../routes/oauth.php');
        });
    }
}
