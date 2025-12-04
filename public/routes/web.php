<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EngineController;

Route::get('/', function () {
    return view('livewire.pages.home');
})->name('home');

Route::get('/catalog', function () {
    return view('livewire.pages.catalog');
})->name('catalog');

Route::get('/delivery', function () {
    return view('livewire.pages.delivery');
})->name('delivery');

Route::get('/about', function () {
    return view('livewire.pages.about');
})->name('about');

Route::get('/contacts', function () {
    return view('livewire.pages.contacts');
})->name('contacts');

Route::get('/engine/{slug}', [EngineController::class, 'show'])
    ->name('engine.show');

require __DIR__ . '/auth.php';
