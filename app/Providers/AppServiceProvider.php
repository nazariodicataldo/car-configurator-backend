<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            $frontendUrl = Config::get('app.frontend_url');
            return "{$frontendUrl}/reset-password?token={$token}&email=". urlencode($user->email);
        });

        Password::defaults(function() {
            $rule = Password::min(8);
 
            return $this->app->isProduction()
                ? $rule->mixedCase()->uncompromised()
                : $rule;
        });
    }
}
