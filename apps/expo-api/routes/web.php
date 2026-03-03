<?php

use App\Http\Controllers\PostmanController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebController::class, 'home']);
Route::get('/docs', [WebController::class, 'docs']);

// ── Postman Collection Downloads ────────────────────────────────────
Route::prefix('docs/postman')->group(function () {
    Route::get('/collections', [PostmanController::class, 'index']);
    Route::get('/all',         [PostmanController::class, 'downloadAll']);
    Route::get('/collection/{slug}',          [PostmanController::class, 'downloadCollection']);
    Route::get('/collection/{slug}/{folder}', [PostmanController::class, 'downloadFolder']);
    Route::get('/environment/{type?}',        [PostmanController::class, 'downloadEnvironment']);
});
