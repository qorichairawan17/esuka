<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Auth\AuthMiddleware;
use App\Http\Middleware\ForbiddenForUserMiddleware;
use App\Http\Controllers\Sync\SyncSuratKuasaController;

Route::prefix('surat-kuasa/sync')->middleware([AuthMiddleware::class, ForBiddenForUserMiddleware::class])->group(function () {
    Route::controller(SyncSuratKuasaController::class)->group(function () {
        Route::get('/', 'index')->name('sync.index');
        Route::post('/fetch-data', 'fetchDataOnDB')->name('sync.fetch-data');
        Route::delete('/delete-data', 'destroy')->name('sync.delete-data');
        Route::get('/show/{id}', 'show')->name('sync.show');
        Route::post('/migrate', 'migrate')->name('sync.migrate');
    });
});
