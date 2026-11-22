<?php

use Illuminate\Support\Facades\Route;
use Modules\Publishing\Http\Controllers\ContentController;
use Modules\Publishing\Http\Controllers\FeedController;
use Modules\Publishing\Http\Controllers\ServicesController;

Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
Route::get('/bn/feed', [FeedController::class, 'indexBn'])->middleware('locale:bn')->name('bn.feed.index');
Route::get('/feed/latest', [FeedController::class, 'latest'])->name('feed.latest');
Route::post('/feed/{post}/react', [FeedController::class, 'react'])->whereNumber('post')->middleware('throttle:feed-reactions')->name('feed.react');
Route::post('/feed/{post}/comments', [FeedController::class, 'comment'])->whereNumber('post')->middleware('throttle:public-forms')->name('feed.comment');

Route::post('/services/proposal', [ServicesController::class, 'proposal'])->middleware('throttle:public-forms')->name('services.proposal');
Route::post('/services/expert-network/apply', [ServicesController::class, 'expert'])->middleware('throttle:public-forms')->name('services.expert');

// Admin-managed content
Route::get('/insights', [ContentController::class, 'insights'])->name('insights.index');
Route::get('/insights/feed.xml', [ContentController::class, 'atom'])->name('insights.feed');
Route::get('/insights/{article}', [ContentController::class, 'article'])->name('insights.show');
Route::get('/ideas', [ContentController::class, 'ideas'])->name('public-ideas.index');
Route::get('/ideas/{idea}', [ContentController::class, 'idea'])->name('public-ideas.show');
Route::post('/ideas/{idea}/support', [ContentController::class, 'vote'])->middleware('throttle:feed-reactions')->name('public-ideas.vote');
Route::get('/people', [ContentController::class, 'people'])->name('people');
Route::get('/people/{person}', [ContentController::class, 'person'])->name('people.show');
Route::get('/partners', [ContentController::class, 'partners'])->name('partners');

Route::get('/gallery', [ContentController::class, 'gallery'])->name('gallery');
Route::get('/search', [ContentController::class, 'search'])->middleware('throttle:60,1')->name('search');
