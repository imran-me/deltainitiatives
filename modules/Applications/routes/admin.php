<?php

use Illuminate\Support\Facades\Route;
use Modules\Applications\Http\Controllers\Admin\CvController;

// Prefixed /admin and named admin.* by the module loader.
Route::get('join-applications/{application}/cv', CvController::class)
    ->middleware('can:inbox.manage')
    ->whereNumber('application')
    ->name('applications.cv');
