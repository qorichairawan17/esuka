<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Auth\AuthMiddleware;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Middleware\Auth\NonAuthMiddleware;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::prefix('auth')->controller(GoogleAuthController::class)->group(function () {
    Route::get('google/', 'redirect')->name('google.redirect');
    Route::get('google/callback', 'callback')->name('google.callback');
});

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('auth.login')->middleware([NonAuthMiddleware::class, 'throttle:login']);
    Route::post('/logout', 'logout')->name('auth.logout');
    Route::post('/register', 'register')->name('auth.register')->middleware([NonAuthMiddleware::class, 'throttle:5,1']);
    Route::post('/google/unlink', [GoogleAuthController::class, 'unlink'])->name('google.unlink')->middleware(AuthMiddleware::class);
});

Route::get('/activate-account/{token}', [ActivationController::class, 'activate'])->name('auth.activate-account');

Route::middleware(NonAuthMiddleware::class)->controller(ResetPasswordController::class)->group(function () {
    Route::get('app/forgot-password', 'index')->name('auth.forgot-password');
    Route::post('/send', 'send')->name('auth.forgot-password.send');
    Route::post('/save', 'save')->name('auth.forgot-password.save');
    Route::get('/reset-password/{token}', 'reset')->name('auth.forgot-password.reset');
});

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha')->name('captcha');
