<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EngineController;
use App\Http\Controllers\AmoAuthController;



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

// amocrm
Route::group(['prefix' => 'amocrm'], function () {
    Route::get('/install', [AmoAuthController::class, 'install'])->name('amocrm.install');
    Route::get('/callback', [AmoAuthController::class, 'callback'])->name('amocrm.callback');
});

//test
Route::get('/amocrm/test-lead', [\App\Http\Controllers\AmoTestController::class, 'test']);


require __DIR__ . '/auth.php';
