<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin settings
    |--------------------------------------------------------------------------
    */

    'settings' => [

        'pagination' => 24,

    ],

    'locales' => [
        'ru',
        'en',
    ],

    'cookie' => 5184000, // 60 дней
    'date_format' => 'd.m.Y H:i',

    // Разделы запрещённые для Редакторов
    'editor_section_banned' => [
        'Additionally',
    ],

    // Зоны доступа для пользователей
    'user_areas' => [
        1 => env('AREA_PUBLIC', 'public'), // Должна быть 1-я область для всего сайта
        2 => env('AREA_ADMIN', 'admin'), // Должна быть 2-я область для админки сайта
    ],

    /*
     * Роли пользователей
     * Нумерация ключей должна быть также в таблице roles
     * user, guest, admin, editor - не меняйте название и очерёдность!
     */
    'user_roles' => [
        1 => 'user', // Зарегистрированный пользователь, должен быть первый
        2 => 'guest', // Не зарегистрированный пользователь, посетитель
        3 => 'admin', // Администратор
        4 => 'editor', // Редактор
    ],

    // Статусы пользователей
    'user_statuses' => [
        'new', // Должен быть первый new статус, т.е. непонятный пользователь
        'strange',
        'dangerous',
        'sociable',
        'wonderful',
    ],

    // Выбор редактора для контента
    'editor' => 'codemirror', // Есть варианты: codemirror, ckeditor,

    // Прилипающая кнопка отправить
    'sticky_submit' => true,

    // Статусы заказов
    'order_statuses' => [
        'new', // Должен быть первый new статус, т.е. новый
        'in_process',
        'completed',
    ],

    // Команды для терминала на странице Дополнительно
    'commands' => [
        'make:module',
        'make:controller',
        'make:model',
        'make:middleware',
        'migrate',
        'migrate:rollback',
        'make:migration',
    ],


    // Разрешённые для загрузки картинки
    'acceptedImagesExt' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
    ],

    // Настройки для SCSS, при изменении настроек необходимо запустить метод \App\Helpers\Upload::resourceInit(); и перекомпилировать стили
    'scss' => [
        'primary-admin' => '#78909c',
        'dark-admin' => '#292b37',
        'light-admin' => '#eceff1',
        'gray-blue' => '#78909c',
        'transition-admin' => '.5',
        'aside-width-icon' => '3rem',
        'aside-width-text' => '15rem',
    ],

    // Картинки - для новых, используйте название как написаны, в конце подставляя свои, например imgBrand
    // Пользователи
    'imgUser' => '/' . env('APP_IMG', 'img') . '/users-photo',
    'imgPathUser' => public_path() . '/' . env('APP_IMG', 'img') . '/users-photo',
    'imgUserDefault' => '/' . env('APP_IMG', 'img') . '/default/no_user.png',

    // Товары
    'imgProduct' => '/' . env('APP_IMG', 'img') . '/product',
    'imgPathProduct' => public_path() . '/' . env('APP_IMG', 'img') . '/product',
    'imgProductDefault' => '/' . env('APP_IMG', 'img') . '/default/no_image.jpg',

    // Галерея товаров
    'imgProductGallery' => '/' . env('APP_IMG', 'img') . '/product-gallery',
    'imgPathProductGallery' => public_path() . '/' . env('APP_IMG', 'img') . '/product-gallery',

    // Максимальные разрешения для картинок
    'imgMaxSizeHD' => 1920,
    'imgMaxSize' => 1280,
    'imgMaxSizeSM' => 500,

    // Максимальное кол-во картинок для Dropzone
    'maxFilesOne' => 1, // Для одиночной загрузки
    'maxFilesMany' => 30, // Для множественной загрузки

];
