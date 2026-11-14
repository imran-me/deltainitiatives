<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AuditLogController;
use Modules\Admin\Http\Controllers\BackupController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\ResourceController;
use Modules\Admin\Http\Controllers\SettingsController;

Route::get('/', DashboardController::class)->middleware('can:dashboard.view')->name('dashboard');

Route::middleware('can:audit.view')->group(function () {
    Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('audit-log/export', [AuditLogController::class, 'export'])->name('audit.export');
});

Route::middleware('can:settings.manage')->group(function () {
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::middleware('can:backups.manage')->group(function () {
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
    Route::get('backups/{name}', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('demo-content', [BackupController::class, 'purgeDemo'])->name('demo.purge');
});

// Generic CRUD for registered resources: /admin/manage/{resource}
Route::prefix('manage/{resource}')->name('resources.')->group(function () {
    Route::get('/', [ResourceController::class, 'index'])->name('index');
    Route::get('create', [ResourceController::class, 'create'])->name('create');
    Route::post('/', [ResourceController::class, 'store'])->name('store');
    Route::get('export', [ResourceController::class, 'export'])->name('export');
    Route::get('{id}', [ResourceController::class, 'show'])->whereNumber('id')->name('show');
    Route::get('{id}/edit', [ResourceController::class, 'edit'])->whereNumber('id')->name('edit');
    Route::put('{id}', [ResourceController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('{id}', [ResourceController::class, 'destroy'])->whereNumber('id')->name('destroy');
});
