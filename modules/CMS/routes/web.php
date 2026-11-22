<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\Support\BanglaServices;
use Modules\CMS\Http\Controllers\BanglaController;
use Modules\CMS\Http\Controllers\LegalController;
use Modules\CMS\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
// Pages folded into the round-3 structure: 301 to their new place.
Route::permanentRedirect('/what-we-do', '/research')->name('what-we-do');
Route::view('/eon', 'cms::eon')->name('eon');
Route::view('/careers', 'cms::careers')->name('careers');
Route::permanentRedirect('/initiators', '/submit-an-idea#recognition')->name('initiators');

// Bangla main pages
Route::prefix('bn')->middleware('locale:bn')->as('bn.')->group(function () {
    Route::get('/', [BanglaController::class, 'home'])->name('home');
    Route::get('/about', [BanglaController::class, 'about'])->name('about');
    Route::get('/research', [BanglaController::class, 'research'])->name('research');
    Route::get('/ideas', [BanglaController::class, 'ideas'])->name('ideas.list');
    Route::get('/insights', [BanglaController::class, 'insights'])->name('insights');
    Route::get('/people', [BanglaController::class, 'people'])->name('people');
    Route::get('/partners', [BanglaController::class, 'partners'])->name('partners');
    Route::view('/services', 'cms::bn.services')->name('services.index');
    Route::view('/eon', 'cms::bn.eon')->name('eon');
    foreach (BanglaServices::SLUGS as $serviceSlug) {
        Route::get('/services/'.$serviceSlug, [BanglaController::class, 'service'])->defaults('slug', $serviceSlug)->name('services.'.$serviceSlug);
    }
});

// v3 static pages (research areas, initiatives, insights, impact, initiators, events, press)
require __DIR__.'/v3.php';

Route::get('/{slug}', [LegalController::class, 'show'])
    ->whereIn('slug', ['privacy', 'terms', 'contributor-agreement'])
    ->name('legal.show');
