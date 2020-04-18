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

    'modules' => [

        // Директория видимости модуля
        env('AREA_PUBLIC', 'publicly') => [

            'Shop' => [ // Название модуля
                'routes' => true, // Есть ли файл роутов в папке модуля
                'webpack' => true, // Есть ли свои стили и скрипты в папке модуля
            ],
            'Form' => [
                'routes' => true,
                'webpack' => false,
            ],
            'Page' => [ // Page - должен стоять последний
                'routes' => true,
                'webpack' => true,
            ],

        ],
        env('AREA_ADMIN', 'admin') => false, // Если false, то модуль без вложенных модулей, т.е. один общий модуль
    ]

];
