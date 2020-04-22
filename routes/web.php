<?php

use App\App;

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


// Если включена авторизация на сайте
if (App::issetModule('Auth')) {
    Auth::routes();

    Route::get('home', 'HomeController@index')->name('home');
}


//Route::get(env('APP_ADMIN', 'admin') . '/login/{key}', 'Voyager\AuthController@login')->middleware(['web'])->name('voyager.login');
/*Route::get("$admin/$key", 'Voyager\AuthController@login')->name('voyager.login');
Route::post("$admin/$key", 'Voyager\AuthController@postLogin')->name('voyager.postlogin');*/


// Подключаем модули
$modules = config('modules.modules');
$modulesPath = config('modules.path');
if ($modules && is_array($modules) && $modulesPath) {
    foreach ($modules as $module => $moduleValue) {

        // Если в настройках указано routes
        if (!empty($moduleValue['routes'])) {

            $routeFile = "{$modulesPath}/{$module}/routes.php";
            if (is_file($routeFile)) {
                require_once $routeFile;
            }
        }
    }


    // По-умолчанию подключаем модуль Page, т.к его маршруты должны идти последнии
    $routeFile = "{$modulesPath}/Page/routes.php";
    if (is_file($routeFile)) {
        require_once $routeFile;
    }
}
