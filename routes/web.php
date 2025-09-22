<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\HomeMiddleware;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\Auth\AuthMiddleware;
use App\Http\Controllers\AuditTrailController;
use App\Http\Middleware\Auth\NonAuthMiddleware;
use App\Http\Middleware\ForbiddenForUserMiddleware;
use App\Http\Controllers\Pengguna\PaniteraController;
use App\Http\Controllers\Pengaturan\AplikasiController;
use App\Http\Controllers\Pengaturan\TestimoniController;
use App\Http\Controllers\Suratkuasa\PembayaranController;
use App\Http\Controllers\Suratkuasa\SuratkuasaController;
use App\Http\Controllers\Pengguna\AdministratorController;
use App\Http\Middleware\Profile\CompleteProfileMiddleware;
use App\Http\Controllers\Suratkuasa\CetakBarcodeController;
use App\Http\Controllers\Pengguna\AdvokatNonAdvokatController;
use App\Http\Controllers\Suratkuasa\LaporanSuratKuasaController;
use App\Http\Controllers\Suratkuasa\VerifikasiSuratKuasaController;

foreach (glob(__DIR__ . '/auth/*.php') as $routeFile) {
    require $routeFile;
}

foreach (glob(__DIR__ . '/panduan/*.php') as $routeFile) {
    require $routeFile;
}

Route::get('/', function () {
    return redirect()->route('app.home');
});

Route::prefix('index')->controller(LandingController::class)->group(function () {
    Route::get('/', 'index')->name('app.home');
    Route::get('/about', 'about')->name('app.about');
    Route::get('/contact', 'contact')->name('app.contact');
    Route::middleware(NonAuthMiddleware::class)->group(function () {
        Route::get('/signin', 'signin')->name('app.signin');
        Route::get('/signup', 'signup')->name('app.signup');
    });
    Route::get('/surat-kuasa/verify/{uuid}', 'verify')->name('app.surat-kuasa.verify');
});

Route::prefix('dashboard')->middleware([AuthMiddleware::class, CompleteProfileMiddleware::class, HomeMiddleware::class])->controller(HomeController::class)->group(function () {
    Route::get('/panel-admin', 'index')->name('dashboard.admin');
    Route::get('/panel-pengguna', 'pengguna')->name('dashboard.pengguna');
});

Route::prefix('surat-kuasa')->middleware([AuthMiddleware::class, CompleteProfileMiddleware::class])->group(function () {
    Route::controller(SuratkuasaController::class)->group(function () {
        Route::get('/pendaftaran', 'index')->name('surat-kuasa.index');
        Route::get('/form/{param}/{klasifikasi}/{id?}', 'form')->name('surat-kuasa.form');
        Route::get('/detail/{id?}', 'detail')->name('surat-kuasa.detail');
        Route::post('/store', 'store')->name('surat-kuasa.store');
        Route::post('/update/{id}', 'update')->name('surat-kuasa.update');
        Route::delete('/destroy/{id}', 'destroy')->name('surat-kuasa.destroy');

        Route::get('/download/{path}', 'downloadFile')->name('surat-kuasa.download');
        Route::get('/doc/preview/{id}/{jenis_dokumen}', 'previewFile')->name('surat-kuasa.preview-file');
    });

    Route::controller(PembayaranController::class)->group(function () {
        Route::get('/pembayaran/{id}', 'index')->name('surat-kuasa.pembayaran');
        Route::post('/pembayaran/store/', 'store')->name('surat-kuasa.pembayaran-store');
        Route::get('/pembayaran/preview/{id}', 'preview')->name('surat-kuasa.pembayaran-preview');
    });

    Route::controller(VerifikasiSuratKuasaController::class)->middleware(ForbiddenForUserMiddleware::class)->group(function () {
        Route::post('/approve', 'approve')->name('surat-kuasa.verifikasi.approve');
        Route::post('/reject', 'reject')->name('surat-kuasa.verifikasi.reject');
    });

    Route::controller(CetakBarcodeController::class)->group(function () {
        Route::get('/barcode/{id?}', 'index')->name('surat-kuasa.barcode');
    });

    Route::controller(LaporanSuratKuasaController::class)->middleware(ForbiddenForUserMiddleware::class)->group(function () {
        Route::get('/laporan', 'index')->name('surat-kuasa.laporan');
    });
});

Route::prefix('pengguna')->middleware([AuthMiddleware::class, ForbiddenForUserMiddleware::class])->group(function () {
    Route::controller(AdvokatNonAdvokatController::class)->group(function () {
        Route::get('/advokat-non-advokat', 'index')->name('advokat.index');
        Route::get('/advokat-non-advokat/form/{param}/{id?}', 'form')->name('advokat.form');
        Route::get('/advokat-non-advokat/detail/{id?}', 'detail')->name('advokat.detail');
        Route::post('/advokat-non-advokat/store', 'store')->name('advokat.store');
        Route::delete('/advokat-non-advokat/destroy/{id}', 'destroy')->name('advokat.destroy');
    });

    Route::controller(PaniteraController::class)->group(function () {
        Route::get('/panitera', 'index')->name('panitera.index');
        Route::get('/panitera/form/{param}/{id?}', 'form')->name('panitera.form');
        Route::post('/panitera/store', 'store')->name('panitera.store');
        Route::delete('/panitera/destroy/{id}', 'destroy')->name('panitera.destroy');
    });

    Route::controller(AdministratorController::class)->group(function () {
        Route::get('/administrator', 'index')->name('administrator.index');
        Route::get('/administrator/form/{param}/{id?}', 'form')->name('administrator.form');
        Route::get('/administrator/detail/{id?}', 'detail')->name('administrator.detail');
        Route::post('/administrator/store', 'store')->name('administrator.store');
        Route::delete('/administrator/destroy/{id}', 'destroy')->name('administrator.destroy');
    });
});

Route::prefix('pengaturan')->middleware(AuthMiddleware::class)->group(function () {
    Route::controller(AplikasiController::class)->middleware(ForBiddenForUserMiddleware::class)->group(function () {
        Route::get('/aplikasi', 'index')->name('aplikasi.index');
        Route::post('/aplikasi/store', 'storeAplikasi')->name('aplikasi.store');

        Route::get('/pembayaran', 'pembayaran')->name('pembayaran.index');
        Route::post('/pembayaran/store', 'storePembayaran')->name('pembayaran.store');

        Route::get('/pejabat-struktural', 'pejabatStruktural')->name('pejabat-struktural.index');
        Route::post('/pejabat-struktural/store', 'storePejabatStruktural')->name('pejabat-struktural.store');
    });

    Route::controller(TestimoniController::class)->group(function () {
        Route::middleware(ForBiddenForUserMiddleware::class)->group(function () {
            Route::get('/testimoni', 'index')->name('testimoni.index');
            Route::get('/testimoni/edit/{id}', 'edit')->name('testimoni.edit');
            Route::post('/testimoni/update/{id}', 'update')->name('testimoni.update');
        });
        Route::post('/testimoni/store', 'store')->name('testimoni.store');
    });
});

Route::prefix('profil')->middleware(AuthMiddleware::class)->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/', 'index')->name('profile.index');
        Route::post('/update', 'update')->name('profile.update');
        Route::post('/change-photo', 'updatePhoto')->name('profile.updatePhoto');
        Route::post('/change-password', 'updatePassword')->name('profile.updatePassword');
        Route::delete('/destroy', 'destroy')->name('profile.destroy');
    });
});

Route::prefix('audit-trail')->middleware([AuthMiddleware::class, ForBiddenForUserMiddleware::class])->group(function () {
    Route::controller(AuditTrailController::class)->group(function () {
        Route::get('/', 'index')->name('audit-trail.index');
        Route::get('/{id}', 'show')->name('audit-trail.show');
    });
});

Route::post('/notifications/mark-as-read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->noContent();
})->middleware(AuthMiddleware::class)->name('notifications.markAsRead');
