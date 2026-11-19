<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\Http\Controllers\LegalController;
use Modules\CMS\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/what-we-do', [PageController::class, 'whatWeDo'])->name('what-we-do');

// v3 static pages (research areas, initiatives, insights, impact, initiators, events, press)
require __DIR__.'/v3.php';

Route::get('/{slug}', [LegalController::class, 'show'])
    ->whereIn('slug', ['privacy', 'terms', 'contributor-agreement'])
    ->name('legal.show');
