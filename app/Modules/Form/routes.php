<?php

Route::namespace('\App\\Modules\\Form\\Controllers')->group(function () {

    Route::post('contact-us', 'FormController@contactUs')->name('post_contact_us');

});
