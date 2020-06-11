<?php

// Route::namespace()->prefix(LaravelLocalization::setLocale()) // Для многоязычной версии сайта, добавить перед ->group(
Route::namespace('\App\\Modules\\Form\\Controllers')->group(function () {

    Route::post('contact-us', 'FormController@contactUs')->name('post_contact_us');

});
