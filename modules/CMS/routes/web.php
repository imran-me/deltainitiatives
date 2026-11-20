<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\Http\Controllers\LegalController;
use Modules\CMS\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
// Pages folded into the round-3 structure: 301 to their new place.
Route::permanentRedirect('/what-we-do', '/research')->name('what-we-do');
Route::permanentRedirect('/gallery', '/feed?view=grid')->name('gallery');
Route::permanentRedirect('/initiators', '/submit-an-idea#recognition')->name('initiators');

// v3 static pages (research areas, initiatives, insights, impact, initiators, events, press)
require __DIR__.'/v3.php';

Route::get('/{slug}', [LegalController::class, 'show'])
    ->whereIn('slug', ['privacy', 'terms', 'contributor-agreement'])
    ->name('legal.show');
