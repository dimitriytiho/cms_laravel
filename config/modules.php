<?php

//$admin = is_file(__DIR__ . '/admin.php') ? include('admin.php') : null;


return [

    /*
    |--------------------------------------------------------------------------
    | Modules settings
    |--------------------------------------------------------------------------
    */

    'path' => app_path(env('APP_MODULES')),
    'path_file' => 'app/' . env('APP_MODULES'),
    'namespace' => 'App\\' . env('APP_MODULES'),

    // Папка с видами
    'views' => 'views',

    // Папка с переводами
    'lang' => 'lang',

    'modules' => [
        'Admin' => [
            'routes' => true,
            'webpack' => true,
        ],
        'Form' => [
            'routes' => true,
            'webpack' => false,
        ],
        'Shop' => [
            'routes' => true,
            'webpack' => true,
        ],
        /*'Auth' => [
            'routes' => true,
            'webpack' => true,
        ],*/
    ],

];
