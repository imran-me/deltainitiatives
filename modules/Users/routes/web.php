<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\Auth\LoginController;
use Modules\Users\Http\Controllers\Auth\PasswordResetController;

// Guest-facing authentication screens for the admin panel.
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('admin.login');
        Route::post('login', [LoginController::class, 'store'])->middleware('throttle:admin-login')->name('admin.login.store');
        Route::get('forgot-password', [PasswordResetController::class, 'request'])->name('admin.password.request');
        Route::post('forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:admin-login')->name('admin.password.email');
        // Named "password.reset" so Laravel's reset notification links here.
        Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
        Route::post('reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:admin-login')->name('admin.password.update');
    });

    Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('admin.logout');
});
