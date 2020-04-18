<?php

Route::namespace('\App\\Modules\\publicly\\Form\\Controllers')->group(function () {

    Route::post('contact-us', 'FormController@contactUs')->name('post_contact_us');

});
