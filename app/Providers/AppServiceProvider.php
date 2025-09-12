<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
    }
}
