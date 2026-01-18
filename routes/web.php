<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\EngineController;
use App\Http\Controllers\AmoAuthController;
use App\Http\Controllers\FormSubmissionController;



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

// amocrm - публичная интеграция (OAuth 2.0)
Route::group(['prefix' => 'amocrm'], function () {
    Route::get('/install', [AmoAuthController::class, 'install'])->name('amocrm.install');
    Route::get('/callback', [AmoAuthController::class, 'callback'])->name('amocrm.callback');

    // Debug route - логирует все запросы к /amocrm/callback
    Route::any('/callback-debug', function (Request $request) {
        \Log::info('AmoCRM CALLBACK DEBUG', [
            'method' => $request->method(),
            'all_params' => $request->all(),
            'url' => $request->url(),
            'query_string' => $request->getQueryString(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);
        return response()->json(['status' => 'logged', 'timestamp' => now()]);
    });
});

// Обработка форм
Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {
    Route::post('/contact-form', [FormSubmissionController::class, 'submitContactForm'])->name('api.contact-form');
    Route::post('/catalog-form', [FormSubmissionController::class, 'submitCatalogForm'])->name('api.catalog-form');
});

Route::get('/amocrm/test', [AmoAuthController::class, 'install']);

require __DIR__ . '/auth.php';
