<?php

use Illuminate\Support\Str;


return [

    /*
    |--------------------------------------------------------------------------
    | Add custom settings
    |--------------------------------------------------------------------------
    */

    'debugbar' => false, // Панель debug
    'online_users' => true, // Показывать пользователей на сайте (обновляется каждую минуту)

    'search' => true, // Поиск по сайту
    'auth' => true, // Включить авторизацию на сайте
    'shop' => true, // Включить интернет-магазин


    // Список используемых локалей
    'locales' => [
        'en',
        'ru',
    ],

    // Перечислить те страницы, которые не нужно индексировать
    'disallow' => [
        'search',
        'search/*',
        //'success',
    ],

    // Кол-во элементов на странице для пагинации
    'pagination' => 24,

    // Настройки для SCSS, при изменении настроек необходимо запустить метод \App\Helpers\Upload::resourceInit(); и перекомпилировать стили
    'scss' => [
        'primary' => '#ff5e5e',
        'dark' => '#000', // #292b37
        'secondary' => '#6c757d',
        'light' => '#eceff1',
        'light-light' => '#fafafa',
        'transition' => '.5',
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

    // Список таблиц информационных блоков (для обновления веб-сайта и пр.), у таблиц должны быть статусы как в массиве page_statuses.
    'list_of_information_block' => [
        
        // Имена таблиц в БД
        'tables' => [
            'pages',
        ],

        // Имена маршрутов из /routes/web.php, маршруты должны быть именованные
        'routes' => [ // Очерёдность должна быть как в массиве tables
            'page',
        ],
    ],

    // Список страниц, которые нужно добавить в sitemap, которых нет в БД
    'list_pages_for_sitemap_no_db' => [

        // Имена таблиц в БД
        'items' => [
            //'contact-us',
            //'order',
        ],

        // Имена маршрутов из /routes/web.php, маршруты должны быть именованные
        'routes' => [ // Очерёдность должна быть как в массиве tables
            //'contact_us',
            //'order',
        ],
    ],

];
