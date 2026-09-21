<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ReportController;

Route::post('/scans', [ScanController::class, 'store']);
Route::get('/scans/{scan}', [ScanController::class, 'show']);
Route::post('/scans/{scan}/cancel', [ScanController::class, 'cancel']);
