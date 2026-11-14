<?php

use Illuminate\Support\Facades\Route;
use Modules\IdeaPortal\Http\Controllers\Admin\IdeaController;

// /admin/ideas — confidential; every route needs at least "ideas.view".
Route::middleware('can:ideas.view')->prefix('ideas')->as('ideas.')->group(function () {
    $code = '[A-Za-z]{2}-[A-Za-z0-9]+-[0-9]{4}';

    Route::get('/', [IdeaController::class, 'index'])->name('index');
    Route::get('export', [IdeaController::class, 'export'])->middleware('can:ideas.export')->name('export');
    Route::get('{idea:code}', [IdeaController::class, 'show'])->where('idea', $code)->name('show');
    Route::get('{idea:code}/files/{file}', [IdeaController::class, 'download'])->where('idea', $code)->whereNumber('file')->scopeBindings()->name('files.download');
    Route::post('{idea:code}/score', [IdeaController::class, 'score'])->where('idea', $code)->middleware('can:ideas.score')->name('score');
    Route::post('{idea:code}/status', [IdeaController::class, 'status'])->where('idea', $code)->middleware('can:ideas.status')->name('status');
});
