<?php

Route::namespace('\App\\Modules\\Blog\\Controllers')->group(function () {

    Route::get('blog', 'BlogController@index')->name('blog_index');
    Route::get('blog/{slug}', 'BlogController@show')->name('blog');

});
