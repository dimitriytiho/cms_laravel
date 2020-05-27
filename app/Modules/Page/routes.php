<?php

Route::namespace('\App\\Modules\\Page\\Controllers')->group(function () {

    Route::get('/', 'PageController@index')->name('index');

    if (config('add.search')) {
        Route::get('search', 'SearchController@index')->name('search');
        Route::post('search-js', 'SearchController@js')->name('search_js');
    }

    Route::get('not-found', 'PageController@notFound')->name('not_found');
    Route::get('contact-us', 'PageController@contactUs')->name('contact_us');
    Route::get('{slug}', 'PageController@show')->name('page');

});
