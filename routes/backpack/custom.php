<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('engines', 'EnginesCrudController');

    // Маршруты для управления медиа в Engine
    Route::post('engine/delete-media', 'EnginesCrudController@deleteMedia')->name('admin.engine.delete-media');
    Route::get('engine/media-list', 'EnginesCrudController@getMediaList')->name('admin.engine.media-list');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
