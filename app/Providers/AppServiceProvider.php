<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register optimization services in production
        if ($this->app->isProduction()) {
            $this->app['config']['app.debug'] = false;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL compatibility
        Schema::defaultStringLength(191);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Optimize database queries - disable strict mode if needed
        // DB::statement("SET SESSION sql_mode=''");

        // Optimize eloquent model loading
        if ($this->app->environment('production')) {
            DB::enableQueryLog();
        }

        // Rate limiting for login
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->string('email')->lower();
            $ip = (string) $request->ip();

            // Create a more specific key by combining the email and IP address.
            $key = $email . '|' . $ip;

            return [
                // Limit attempts based on the email and IP combination.
                // Allows 5 attempts per minute for a specific user from a specific IP.
                Limit::perMinute(5)->by($key),

                // As an additional safeguard, limit attempts by IP address only.
                // This prevents a single IP from spamming the login form with many different emails.
                // Allows 20 attempts per minute from a single IP.
                Limit::perMinute(20)->by($ip),
            ];
        });

        // Rate limiting for API
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiting for global requests
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });
    }
}
