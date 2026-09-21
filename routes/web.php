<?php

use App\Http\Controllers\ScanController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('marketing.home'));
Route::get('/seo-audit', fn() => view('tools.seo'));
Route::get('/adsense-audit', fn() => view('tools.adsense'));
Route::get('/speed-audit', fn() => view('tools.speed'));
Route::get('/link-audit', fn() => view('tools.link'));
Route::get('/methodology', fn() => view('legal.methodology'));
Route::get('/guides', fn() => view('marketing.guides'));
Route::get('/bot', fn() => view('legal.bot'));
Route::get('/security', fn() => view('legal.security'));

Route::post('/api/scans', [ScanController::class, 'store']);
Route::get('/api/scans/{scan}', [ScanController::class, 'show']);
Route::get('/scans/{scan}/stream', [ScanController::class, 'stream']);
Route::get('/scans/{scan}', [ScanController::class, 'progress']);
Route::post('/api/scans/{scan}/cancel', [ScanController::class, 'cancel']);
Route::get('/report/{scan}', [ReportController::class, 'show']);
Route::get('/api/report/{scan}/csv', [ReportController::class, 'csv']);
