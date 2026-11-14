<?php

use Illuminate\Support\Facades\Route;
use Modules\Partnerships\Http\Controllers\PartnerController;

Route::get('/partner-with-us', [PartnerController::class, 'index'])->name('partner.index');
Route::post('/partner-with-us', [PartnerController::class, 'store'])->middleware('throttle:public-forms')->name('partner.store');
Route::get('/partner-with-us/brochure', [PartnerController::class, 'brochure'])->name('partner.brochure');
