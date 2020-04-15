<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Если выключен веб-сайт, то редирект на страницу /public/error.php
if (config('add.site_off')) {
    Route::domain(env('APP_URL'))->group(function () {
        header('Location: ' . env('APP_URL') . '/error.php');
        die;
    });
}

// Если в запросе /public, то сделается редирект на без /public
$url = \Illuminate\Http\Request::capture()->url();
$public = '/public';
if (stripos($url, $public)) {
    $url = str_replace($public, '', $url);
    header("Location: $url");
    die;
}

/*Route::get('news/{alias}', function () {
    return view('page.index', compact('getMeta'));
});*/

// Если включен shop
if (env('APP_SHOP', null)) {

    // Shop controllers
    Route::post('make-order', 'Shop\CartController@makeOrder')->name('make_order');
    Route::get('cart/{product_id}/destroy', 'Shop\CartController@destroy')->name('cart_destroy');
    Route::get('cart/{product_id}/minus', 'Shop\CartController@minus')->name('cart_minus');
    Route::get('cart/{product_id}/plus', 'Shop\CartController@plus')->name('cart_plus');
    Route::get('cart/show', 'Shop\CartController@show')->name('cart_show');
    Route::get('cart', 'Shop\CartController@index')->name('cart');
    Route::get('catalog', 'Shop\CategoryController@index')->name('catalog');
    Route::get('category/{slug}', 'Shop\CategoryController@show')->name('category');
    Route::get('product/{slug}', 'Shop\ProductController@show')->name('product');
}

Route::get('/', 'PageController@index')->name('main');
Route::get('not-found', 'PageController@notFound')->name('not_found');
Route::get('contact-us', 'PageController@contactUs')->name('contact_us');
Route::post('contact-us', 'FormController@contactUs')->name('post_contact_us');


// Если включена авторизация на сайте
if (env('SITE_AUTH', null)) {
    Auth::routes();

    Route::get('home', 'HomeController@index')->name('home');
    /*Route::post('login', 'AuthNew\LoginNewController@login');
    Route::post('register', 'AuthNew\RegisterNewController@register');*/

    // Если нет, то выводим отдельный вход в админку
} else {

    $key = \App\Helpers\Upload::getKeyAdmin();
    $keyRoute = "enter/{$key}";

    Route::post($keyRoute, 'Admin\EnterController@enterPost')->name('enter_post')->middleware('banned-ip');
//Route::post($keyRoute, 'Auth\LoginController@login')->name('enter_post');
    Route::get($keyRoute, 'Admin\EnterController@index')->name('enter');
//Route::match(['get', 'post'], "/enter/{$key}", 'Admin\EnterController@index')->name('enter');
}

$admin = env('APP_ADMIN', 'admin');
Route::prefix($admin)->name('admin.')->middleware('admin')->group(function () {
    //Route::post('/menu/index', 'Admin\MenuController@index')->name('menu.index.post');
    require_once 'admin.php'; // Маршруты для админки
});

/*Route::group(['prefix' => $admin], function () { //, 'middleware' => ['web', 'admin.user', 'is_admin_editor']

    Voyager::routes();

    // Изменён метод logout из админки
    Route::post('logout', 'Voyager\AuthController@logout')->name('voyager.logout');

    // Дополнительные маршруты для админки
    Route::get('files', 'Voyager\FilesController@index')->name('files');
});*/

//Route::get(env('APP_ADMIN', 'admin') . '/login/{key}', 'Voyager\AuthController@login')->middleware(['web'])->name('voyager.login');
/*Route::get("$admin/$key", 'Voyager\AuthController@login')->name('voyager.login');
Route::post("$admin/$key", 'Voyager\AuthController@postLogin')->name('voyager.postlogin');*/

Route::get('/{slug}', 'PageController@show')->name('page');


/*Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');*/
