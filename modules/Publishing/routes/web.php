<?php

use Illuminate\Support\Facades\Route;
use Modules\Publishing\Http\Controllers\FeedController;
use Modules\Publishing\Http\Controllers\ServicesController;

Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
Route::get('/feed/latest', [FeedController::class, 'latest'])->name('feed.latest');
Route::post('/feed/{post}/react', [FeedController::class, 'react'])->whereNumber('post')->middleware('throttle:feed-reactions')->name('feed.react');
Route::post('/feed/{post}/comments', [FeedController::class, 'comment'])->whereNumber('post')->middleware('throttle:public-forms')->name('feed.comment');

Route::post('/services/proposal', [ServicesController::class, 'proposal'])->middleware('throttle:public-forms')->name('services.proposal');
Route::post('/services/expert-network/apply', [ServicesController::class, 'expert'])->middleware('throttle:public-forms')->name('services.expert');
