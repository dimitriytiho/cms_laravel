<?php

/*
 * Здесь писать новые папки и файлы.
 * Чтобы сформировать все файлы запустить метод \App\Widgets\Upload\Upload::allFilesToArr();
 * Здесь указаны все файлы, которые изменялись, но все они напрядли будут изменятся.
 */

return [

    // App
    'app/Exports',
    'app/Helpers',
    'app/Imports',
    'app/Lib',
    'app/Mail',
    'app/Modules',
    'app/Widgets',

    'app/Main.php',
    'app/User.php',


    // App Laravel
    'app/Providers/AppServiceProvider.php',
    'app/Http/Kernel.php',
    'app/Console/Kernel.php',
    'app/Console/Commands',
    'app/Exceptions/Handler.php',
    'app/Http/Controllers/Controller.php',


    // Middleware
    'app/Http/Middleware/AccessIpAdmin.php',
    'app/Http/Middleware/Admin.php',
    'app/Http/Middleware/BannedIp.php',
    'app/Http/Middleware/OnlineUsers.php',


    // Config
    'config/app.php',
    'config/logging.php',
    'config/database.php',

    'config/add.php',
    'config/admin.php',
    'config/modules.php',
    'config/shop.php',


    // Database
    'database/migrations/2019_06_13_104905_create_pages_table.php',
    'database/migrations/2019_06_13_111039_create_forms_table.php',
    'database/migrations/2019_06_14_104905_create_uploads_table.php',
    'database/migrations/2019_07_10_100248_create_roles_table.php',
    'database/migrations/2019_06_20_084521_change_users_table.php',
    'database/migrations/2019_08_07_124549_create_settings_table.php',
    'database/migrations/2019_08_07_140457_create_menu_name_table.php',
    'database/migrations/2019_08_07_140457_create_menu_table.php',
    'database/migrations/2020_02_05_071222_create_categories_table.php',
    'database/migrations/2020_02_05_071240_create_products_table.php',
    'database/migrations/2020_02_05_073628_create_category_product_table.php',
    'database/migrations/2020_02_05_204154_create_orders_table.php',
    'database/migrations/2020_02_05_205458_create_order_product_table.php',
    'database/migrations/2020_02_07_064509_create_users_last_data_table.php',
    'database/migrations/2020_02_09_141027_create_banned_ip_table.php',
    'database/migrations/2020_03_09_203758_create_product_gallery_table.php',
    'database/migrations/2020_05_16_094455_create_filter_groups_table.php',
    'database/migrations/2020_05_16_094505_create_filter_values_table.php',
    'database/migrations/2020_05_16_094551_create_filter_products_table.php',


    // Resources
    'resources/lang',
    'resources/views/vendor/laravel-log-viewer',


    // Routes
    'routes/web.php',


    // Storage
    'storage/online',

];
