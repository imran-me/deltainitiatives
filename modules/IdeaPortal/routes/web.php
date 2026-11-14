<?php

use Illuminate\Support\Facades\Route;
use Modules\IdeaPortal\Http\Controllers\IdeaSubmissionController;
use Modules\IdeaPortal\Http\Controllers\VerifyReceiptController;

// Submit an Idea — English (main) and Bangla (/bn) versions share one controller.
$portal = function () {
    Route::get('/submit-an-idea', [IdeaSubmissionController::class, 'create'])->name('ideas.create');
    Route::post('/submit-an-idea', [IdeaSubmissionController::class, 'store'])->middleware('throttle:idea-submissions')->name('ideas.store');
    Route::get('/submit-an-idea/received', [IdeaSubmissionController::class, 'received'])->name('ideas.received');
};

$portal();
Route::prefix('bn')->middleware('locale:bn')->as('bn.')->group($portal);

// Public receipt verification (Idea ID + fingerprint -> match / no match; never reveals content).
Route::get('/verify', [VerifyReceiptController::class, 'show'])->name('ideas.verify');
Route::post('/verify', [VerifyReceiptController::class, 'check'])->middleware('throttle:verify')->name('ideas.verify.check');
