<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Import Semua Controller
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GeojsonController;
use App\Http\Controllers\Admin\LahanController;
use App\Http\Controllers\Admin\ImportLahanController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ClusteringController;

/*
|--------------------------------------------------------------------------
| Public / User Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('access'); // Pastikan file berada di resources/views/access.blade.php
})->name('access');

// Dashboard User dipindah ke path /dashboard
Route::get('/dashboard', function () {
    return view('user.dashboard');
})->name('user.dashboard');

// Rute peta bisa diakses publik
Route::get('admin/peta', [MapController::class, 'index'])->name('map.view');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Login & Logout)
|--------------------------------------------------------------------------
*/

// Menampilkan halaman login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Memproses data login
Route::post('/login', [AuthController::class, 'login']);

// Memproses logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes (Dilindungi Middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen GeoJSON
    Route::controller(GeojsonController::class)->group(function () {
        Route::get('/geojson', 'index')->name('geojson.index');
        Route::post('/geojson/upload', 'upload')->name('geojson.upload');
        Route::post('/geojson/import', 'import')->name('geojson.import');
        Route::get('/geojson/import-all', 'importAll')->name('geojson.importAll');
        Route::delete('/geojson/{file}', 'delete')->name('geojson.delete');
    });

    // Manajemen Lahan
    Route::controller(LahanController::class)->group(function () {
        Route::get('/lahan', 'index')->name('lahan.index');
        Route::post('/lahan/store', 'store')->name('lahan.store');
        Route::put('/lahan/update/{id}', 'update')->name('lahan.update'); // Diperbaiki agar seragam menggunakan controller grouping
        Route::delete('/lahan/delete/{id}', 'destroy')->name('lahan.delete');

        // Fitur Export Laporan
        Route::get('/lahan/export-pdf', 'exportPdf')->name('lahan.export.pdf');
        Route::get('/lahan/export-csv', 'exportCsv')->name('lahan.export.csv');
    });
    Route::controller(ClusteringController::class)->group(function () {
        Route::get('/clustering', 'index')->name('clustering.index');
        Route::post('/clustering/process', 'process')->name('clustering.process');
    });
    // Route untuk Manajemen CSV
    Route::controller(ImportLahanController::class)->group(function () {
        Route::get('/import', 'index')->name('import.index');
        Route::post('/import-lahan/upload', 'upload')->name('import.upload');
        Route::get('/import-lahan/process/{filename}', 'process')->name('import.process');
        Route::delete('/import-lahan/delete/{filename}', 'delete')->name('import.delete');
    });
    Route::controller(AccountController::class)->group(function () {
        Route::get('/account', 'index')->name('account.index');
        Route::post('/account/store', 'store')->name('account.store');
        Route::put('/account/update/{id}', 'update')->name('account.update');
        Route::delete('/account/delete/{id}', 'destroy')->name('account.delete');
    });
});

/*
|--------------------------------------------------------------------------
| Debugging
|--------------------------------------------------------------------------
*/

Route::get('/test-geojson', function () {
    $files = Storage::disk('local')->files('private/geojson');
    dd($files);
});
