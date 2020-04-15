<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Add custom settings
    |--------------------------------------------------------------------------
    */

    'site_off' => false, // Выключить работу сайта, передать true
    'debugbar' => false,


    // Список используемых локалей
    'locales' => [
        'en',
        'ru',
    ],

    // Если не нужно индексировать сайт, то true, если нужно, то false
    'not_index_website' => true,

    // Перечислить те страницы, которые не нужно индексировать
    'disallow' => [
        //'success',
        //'search',
        //'search/*',
    ],

    // Настройки для SCSS, при изменении настроек необходимо запустить метод App\Helpers\Upload::scssInit и перекомпилировать стили
    'scss' => [
        'primary' => '#40c4ff',
        'dark' => '#292b37',
        'light' => '#eceff1',
        'gray-blue' => '#78909c',
        'light-light' => '#fafafa',
        'transition' => '.5',
    ],
    'scss-admin' => [
        'primary-admin' => '#78909c',
        'dark-admin' => '#292b37',
        'light-admin' => '#eceff1',
        'transition-admin' => '.5',
        'path-img' => "'../" . env('APP_IMG', 'img') . "/'",
        'aside-width-icon' => '3rem',
        'aside-width-text' => '15rem',
    ],
    'height' => 600,

    // Указать IP, для которых запрещён доступ к сайту после этого нужно запустить команду \App\Helpers\Upload::htaccess();
    'banned_ip' => [
        '',
    ],

    'development' => [
        'Developer' => 'Dmitriy Konovalov',
        'Email' => 'dimitriyyuliya@gmail.com',
        'Facebook' => 'https://www.facebook.com/dimitriyyuliya',
        'From' => 'Moscow, Russia',
        'Language' => 'Russian',
        'Doctype' => 'HTML5',
        'IDE' => 'PHPStorm, Sublime Text, Photoshop, Illustrator',
    ],

    // Папка для картинок
    'img' => '/' . env('APP_IMG', 'img'),
    'imgPath' => public_path() . '/' . env('APP_IMG', 'img'),

    // Протокол и домен
    'protocol' => \Illuminate\Support\Str::before(env('APP_URL'), '://'),
    'domain' => \Illuminate\Support\Str::after(env('APP_URL'), '://'),

    // Статусы страниц (неактивная должна стоять первой)
    'page_statuses' => [
        'inactive',
        'active',
    ],

    // Список таблиц информационных блоков (для обновления веб-сайта и пр.), у таблиц должный быть статусы активная и черновик.
    'list_of_information_block_tables' => [
        'pages',
    ],

    // Список страниц, которые нужно добавить в sitemap, которых нет в БД
    'list_pages_for_sitemap_no_db' => [
        'contact-us',
    ],

];
