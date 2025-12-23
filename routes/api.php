<?php

use App\Http\Controllers\Api\LahanGeojsonController;
use App\Http\Controllers\Admin\ImportLahanController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return ['status' => 'API OK'];
});

Route::get('/lahan-geojson', [LahanGeojsonController::class, 'index']);


Route::get('/admin/test-files', [App\Http\Controllers\Admin\ImportLahanController::class, 'testApi']);

Route::get('/cek-csv', [ImportLahanController::class, 'apiCekFiles']);