<?php

use Illuminate\Support\Facades\Route;
use Modules\Applications\Http\Controllers\JoinController;

// Join Us — English (main) and Bangla (/bn) versions share one controller.
$join = function () {
    Route::get('/join-us', [JoinController::class, 'create'])->name('join.create');
    Route::post('/join-us', [JoinController::class, 'store'])->middleware('throttle:public-forms')->name('join.store');
};

$join();
Route::prefix('bn')->middleware('locale:bn')->as('bn.')->group($join);
