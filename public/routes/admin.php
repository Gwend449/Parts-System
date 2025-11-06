<?php

use App\Http\Controllers\Admin\EngineController;

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::resource('engines', EngineController::class);
});
