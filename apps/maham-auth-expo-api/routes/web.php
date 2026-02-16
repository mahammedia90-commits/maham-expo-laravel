<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::get('/docs', function () {
    return view('welcome');
});

 
Route::get('/', function () {
    return  view('home');
});


Route::get('/reset-password', function() { 
    return view('reset-password');
})->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');
