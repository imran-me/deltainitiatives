<?php

use Illuminate\Support\Facades\Route;
use Modules\Partnerships\Http\Controllers\PartnerController;

// The Partners page is now /partners (modules/CMS/routes/v3.php); keep the old URL working.
Route::permanentRedirect('/partner-with-us', '/partners')->name('partner.index');
Route::post('/partner-with-us', [PartnerController::class, 'store'])->middleware('throttle:public-forms')->name('partner.store');
Route::get('/partner-with-us/brochure', [PartnerController::class, 'brochure'])->name('partner.brochure');
