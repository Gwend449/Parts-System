<?php

use App\Livewire\Admin\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin;

Route::view('/', 'index');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->prefix('admin')->as('admin.')->group(function () {
    Route::view('/dashboard', Dashboard::class)->name('dashboard');

    Route::view('/brands', 'livewire.admin.stub')->name('brands');
    Route::view('/models', 'livewire.admin.stub')->name('models');
    Route::view('/engines', 'livewire.admin.stub')->name('engines');
});

require __DIR__.'/auth.php';
