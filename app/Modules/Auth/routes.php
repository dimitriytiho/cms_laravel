<?php

if (config('add.auth')) {

    Route::namespace('\App\\Modules\\Auth\\Controllers')->group(function () {

        Route::get('home', 'HomeController@index')->name('home');
        Route::get('login', 'LoginController@showLoginForm')->name('login');
        Route::post('login', 'LoginController@login')->name('login_post');
        Route::get('logout', 'LoginController@logout')->name('logout');
        Route::post('password/confirm', 'ConfirmPasswordController@confirm')->name('password.confirm_post');
        Route::get('password/confirm', 'ConfirmPasswordController@confirm')->name('password.confirm');
        Route::get('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register')->name('register_post');

    });
}
