<?php


// Routes import export
Route::get('import-export', 'Admin\importExportController@view')->name('import_export');
// Route export User
Route::get('export-user', 'Admin\importExportController@exportUser')->name('export_user');

// Если включен shop
if (env('APP_SHOP', null)) {

    // Product
    Route::get('export-product', 'Admin\importExportController@exportProduct')->name('export_product');
    Route::post('import-product', 'Admin\importExportController@importProduct')->name('import_product');
    // Category
    Route::get('export-category', 'Admin\importExportController@exportCategory')->name('export_category');
    Route::post('import-category', 'Admin\importExportController@importCategory')->name('import_category');


    // Shop controllers
    Route::post('product-add-category', 'Admin\CategoryProductController@productAddCategory')->name('product_add_category');
    Route::post('product-destroy-category', 'Admin\CategoryProductController@productDestroyCategory')->name('product_destroy_category');

    Route::resource('order', 'Admin\OrderController')->only(['index', 'show', 'update', 'destroy']);
    Route::resource('category', 'Admin\CategoryController')->except(['show']);
    Route::resource('product', 'Admin\ProductController')->except(['show']);
}


// Website controllers resource
Route::resource('form', 'Admin\FormController')->only(['index', 'show', 'destroy']);
Route::resource('page', 'Admin\PageController')->except(['show']);
Route::resource('user', 'Admin\UserController')->except(['show']);
Route::resource('user-banned-ip', 'Admin\BannedIpController')->only(['index', 'show', 'destroy']);
Route::resource('menu-name', 'Admin\MenuNameController')->except(['show']);
Route::resource('menu', 'Admin\MenuController')->except(['show']);
Route::resource('setting', 'Admin\SettingController')->except(['show']);


// Website add controllers
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::match(['get','post'],'additionally', 'Admin\AdditionallyController@index')->name('additionally');
Route::get('/additionally/files', 'Admin\AdditionallyController@files')->name('files');


// Add routes get
Route::get('locale/{locale}', 'Admin\MainController@locale')->name('locale');
Route::get('logout', 'Admin\UserController@logout')->name('logout');
Route::get('/', 'Admin\MainController@index')->name('main');

// Add routes post
Route::post('img-remove', 'Admin\ImgUploadController@remove')->name('img_remove');
Route::post('img-upload', 'Admin\ImgUploadController@upload')->name('img_upload');
Route::post('user-change-password', 'Admin\MainController@userChangePassword');
Route::post('cyrillic-to-latin', 'Admin\MainController@cyrillicToLatin');

// Если не включена авторизация на сайте
if (!env('SITE_AUTH', null)) {
    Route::post('to-change-key', 'Admin\MainController@toChangeKey');
}
