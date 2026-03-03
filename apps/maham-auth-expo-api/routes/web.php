<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/', [WebController::class, 'home']);
Route::get('/docs', [WebController::class, 'docs']);

Route::get('/reset-password', [WebController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');
