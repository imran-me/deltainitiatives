<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\SeoController;

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
