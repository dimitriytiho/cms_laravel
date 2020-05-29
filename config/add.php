<?php

use Illuminate\Support\Str;


return [

    /*
    |--------------------------------------------------------------------------
    | Add custom settings
    |--------------------------------------------------------------------------
    */

    'site_off' => false, // Выключить работу сайта, передать true
    'debugbar' => false,
    'online_users' => true, // Показывать пользователей на сайте (обновляется каждую минуту)

    'search' => true, // Поиск по сайту
    'auth' => true, // Включить авторизацию на сайте
    'shop' => true, // Включить интернет-магазин


    // Список используемых локалей
    'locales' => [
        'en',
        'ru',
    ],

    // Если не нужно индексировать сайт, то true, если нужно, то false
    'not_index_website' => true,

    // Перечислить те страницы, которые не нужно индексировать
    'disallow' => [
        'search',
        'search/*',
        //'success',
    ],

    // Кол-во элементов на странице для пагинации
    'pagination' => 24,

    // Настройки для SCSS, при изменении настроек необходимо запустить метод App\Helpers\Upload::scssInit и перекомпилировать стили
    'scss' => [
        'primary' => '#40c4ff',
        'dark' => '#292b37',
        'secondary' => '#6c757d',
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
        'Framework' => 'Laravel',
        'IDE' => 'PHPStorm, Visual Studio, Sublime Text, Photoshop, Illustrator',
        'Brand' => 'OmegaKontur',
    ],

    // Папка для картинок
    'img' => '/' . env('APP_IMG', 'img'),
    'imgPath' => public_path() . '/' . env('APP_IMG', 'img'),

    // Протокол и домен
    'protocol' => Str::before(env('APP_URL'), '://'),
    'domain' => Str::after(env('APP_URL'), '://'),

    // Статусы страниц
    'page_statuses' => [
        'inactive', // Неактивная должна стоять первой
        'active', // Активная должна стоять второй
    ],

    // Список таблиц информационных блоков (для обновления веб-сайта и пр.), у таблиц должный быть статусы как в массиве page_statuses.
    'list_of_information_block' => [
        'tables' => [
            'pages',
        ],
        'routes' => [ // Очерёдность должна быть как в массиве tables
            '',
        ],
    ],

    // Список страниц, которые нужно добавить в sitemap, которых нет в БД
    'list_pages_for_sitemap_no_db' => [
        'contact-us',
    ],

    'recaptcha_public_key' => '',
    'recaptcha_secret_key' => '',
    'smsru' => '',

];
