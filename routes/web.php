<?php

use Illuminate\Support\Facades\Route;


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/admin/login', function () {
    return 'Admin Login Page';
})->name('filament.admin.auth.login');
