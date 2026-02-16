<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('home');
});