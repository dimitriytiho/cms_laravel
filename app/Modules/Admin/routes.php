<?php

use App\App;


$admin = env('APP_ADMIN', 'admin');
$namespace = '\App\\Modules\\admin\\Controllers';

//Route::namespace($namespace)->prefix($admin)->get('/', 'MainController@index')->name('admin.main')->middleware('admin');

// Страница входа в админку
Route::namespace($namespace)->name('enter')->group(function () {

    $key = \App\Helpers\Upload::getKeyAdmin();
    $keyRoute = "enter/{$key}";
    Route::post($keyRoute, 'EnterController@enterPost')->name('_post')->middleware('banned-ip');
    Route::get($keyRoute, 'EnterController@index');

});


// Роуты для админки
Route::namespace($namespace)->prefix($admin)->name('admin.')->middleware('admin')->group(function () {
    //Route::post('/menu/index', 'Admin\MenuController@index')->name('menu.index.post');

    // Routes import export
    Route::get('import-export', 'importExportController@view')->name('import_export');
    // Route export User
    Route::get('export-user', 'importExportController@exportUser')->name('export_user');

    // Если включен shop
    if (App::issetModule('Shop')) {

        // Product
        Route::get('export-product', 'importExportController@exportProduct')->name('export_product');
        Route::post('import-product', 'importExportController@importProduct')->name('import_product');
        // Category
        Route::get('export-category', 'importExportController@exportCategory')->name('export_category');
        Route::post('import-category', 'importExportController@importCategory')->name('import_category');


        // Shop controllers
        Route::post('product-add-category', 'CategoryProductController@productAddCategory')->name('product_add_category');
        Route::post('product-destroy-category', 'CategoryProductController@productDestroyCategory')->name('product_destroy_category');

        Route::resource('order', 'OrderController')->only(['index', 'show', 'update', 'destroy']);
        Route::resource('category', 'CategoryController')->except(['show']);
        Route::resource('product', 'ProductController')->except(['show']);
    }


    // Website controllers resource
    Route::resource('form', 'FormController')->only(['index', 'show', 'destroy']);
    Route::resource('page', 'PageController')->except(['show']);
    Route::resource('user', 'UserController')->except(['show']);
    Route::resource('user-banned-ip', 'BannedIpController')->only(['index', 'show', 'destroy']);
    Route::resource('menu-name', 'MenuNameController')->except(['show']);
    Route::resource('menu', 'MenuController')->except(['show']);
    Route::resource('setting', 'SettingController')->except(['show']);


    // Website add controllers
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::match(['get','post'],'additionally', 'AdditionallyController@index')->name('additionally');
    Route::get('/additionally/files', 'AdditionallyController@files')->name('files');


    // Add routes get
    Route::get('locale/{locale}', 'MainController@locale')->name('locale');
    Route::get('logout', 'UserController@logout')->name('logout');
    Route::get('/', 'MainController@index')->name('main');

    // Add routes post
    Route::post('img-remove', 'ImgUploadController@remove')->name('img_remove');
    Route::post('img-upload', 'ImgUploadController@upload')->name('img_upload');
    Route::post('user-change-password', 'MainController@userChangePassword');
    Route::post('cyrillic-to-latin', 'MainController@cyrillicToLatin');

    // Если не включена авторизация на сайте
    if (!App::issetModule('Auth')) {
        Route::post('to-change-key', 'MainController@toChangeKey');
    }
});
