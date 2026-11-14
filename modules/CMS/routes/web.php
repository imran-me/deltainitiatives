<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\Http\Controllers\LegalController;
use Modules\CMS\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/what-we-do', [PageController::class, 'whatWeDo'])->name('what-we-do');

Route::get('/{slug}', [LegalController::class, 'show'])
    ->whereIn('slug', ['privacy', 'terms', 'contributor-agreement'])
    ->name('legal.show');
