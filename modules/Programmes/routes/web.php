<?php

use Illuminate\Support\Facades\Route;
use Modules\Programmes\Http\Controllers\EventController;
use Modules\Programmes\Http\Controllers\EventRegistrationController;
use Modules\Programmes\Http\Controllers\ProgrammeController;

Route::get('/programmes', [ProgrammeController::class, 'index'])->name('programmes.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event:slug}/register', [EventRegistrationController::class, 'store'])->middleware('throttle:public-forms')->name('events.register');
