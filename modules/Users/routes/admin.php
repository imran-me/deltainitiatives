<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\Admin\UserController;
use Modules\Users\Http\Controllers\ProfileController;

Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');

Route::middleware('can:users.manage')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/send-reset', [UserController::class, 'sendReset'])->name('users.send-reset');
});
