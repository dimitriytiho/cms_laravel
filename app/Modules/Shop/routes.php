<?php

if (config('add.shop')) {

    Route::namespace('\App\\Modules\\Shop\\Controllers')->group(function () {
        Route::post('make-order', 'CartController@makeOrder')->name('make_order');
        Route::get('cart/{product_id}/destroy', 'CartController@destroy')->name('cart_destroy');
        Route::get('cart/{product_id}/minus', 'CartController@minus')->name('cart_minus');
        Route::get('cart/{product_id}/plus', 'CartController@plus')->name('cart_plus');
        Route::get('cart/show', 'CartController@show')->name('cart_show');
        Route::get('cart', 'CartController@index')->name('cart');
        Route::get('catalog', 'CategoryController@index')->name('catalog');
        Route::get('category/{slug}', 'CategoryController@show')->name('category');
        Route::get('product/{slug}', 'ProductController@show')->name('product');

    });
}
