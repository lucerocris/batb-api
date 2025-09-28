<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResetPasswordController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/forgot-password', 'auth.forgot-password')->middleware('guest')->name('password.request');

Route::view('/reset-password/{token}', 'auth.reset-password')->middleware('guest')->name('password.reset');