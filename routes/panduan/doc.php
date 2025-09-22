<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panduan\PanduanController;

Route::prefix('panduan')->controller(PanduanController::class)->name('panduan.')->group(function () {
    Route::get('/{slug?}', 'show')->where('slug', '.*')->name('show');
});
