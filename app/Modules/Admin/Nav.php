<?php


namespace App\Modules\Admin;


class Nav
{
    // ОСНОВНОЕ ЛЕВОЕ МЕНЮ ADMIN этот (файл по-умолчанию не обновляется)
    // Не менять нумерацию! Если надо можно переставить местами и добавить другие цифры ключей.
    public static function menuLeft() {
        $menu = [];
        $menu = $menu + [
                1 => [
                    'title' => 'Dashboard',
                    'controller' => 'Main',
                    'parent_id' => null,
                    'slug' => '/',
                    'request' => 'main',
                    'item' => 'dashboard',
                ],
            ];

        // Если включен shop
        if (config('add.shop')) {
            $menu = $menu + [
                    2 => [
                        'title' => 'Orders',
                        'controller' => 'Order',
                        'parent_id' => null,
                        'slug' => '/order',
                        'request' => 'order',
                        'item' => 'shopping_cart',
                        'count' => 'orders',
                    ],
                ];
        }

        $menu = $menu + [
                3 => [
                    'title' => 'Forms',
                    'controller' => 'Form',
                    'parent_id' => null,
                    'slug' => '/form',
                    'request' => 'form',
                    'item' => 'insert_comment',
                    'count' => 'forms',
                ],
            ];

        // Если включен shop
        if (config('add.shop')) {
            $menu = $menu + [
                    4 => [
                        'title' => 'Categories',
                        'controller' => 'Category',
                        'parent_id' => null,
                        'slug' => '/category',
                        'request' => 'category',
                        'item' => 'account_tree',
                        'count' => 'categories', // Название таблицы
                    ],
                    41 => [
                        'title' => 'Create',
                        'controller' => 'Category',
                        'parent_id' => 4,
                        'slug' => '/category/create',
                        'request' => 'category',
                        'item' => 'add',
                    ],
                    5 => [
                        'title' => 'Products',
                        'controller' => 'Product',
                        'parent_id' => null,
                        'slug' => '/product',
                        'request' => 'product',
                        'item' => 'all_inbox',
                        'count' => 'products', // Название таблицы
                    ],
                    51 => [
                        'title' => 'Create',
                        'controller' => 'Product',
                        'parent_id' => 5,
                        'slug' => '/product/create',
                        'request' => 'product',
                        'item' => 'add',
                    ],

                    // Filters
                    20 => [
                        'title' => 'Filter_value',
                        'controller' => 'FilterValue',
                        'parent_id' => null,
                        'slug' => '/filter-value',
                        'request' => 'filter',
                        'item' => 'tune',
                        'add' => 22, // Добавляется Filter_groups
                    ],
                    21 => [
                        'title' => 'Create_item',
                        'controller' => 'FilterValue',
                        'parent_id' => 20,
                        'slug' => '/filter-value/create',
                        'request' => 'filter',
                        'item' => 'add',
                    ],
                    22 => [
                        'title' => 'Filter_group',
                        'controller' => 'FilterGroup',
                        'parent_id' => 20,
                        'slug' => '/filter-group',
                        'request' => 'filter',
                        'item' => 'tune',
                        'add' => 20, // Добавляется Filter_value
                    ],
                    23 => [
                        'title' => 'Create_group',
                        'controller' => 'FilterGroup',
                        'parent_id' => 22,
                        'slug' => '/filter-group/create',
                        'request' => 'filter',
                        'item' => 'add',
                    ],
                ];
        }

        $menu = $menu + [
                6 => [
                    'title' => 'Pages',
                    'controller' => 'Page',
                    'parent_id' => null,
                    'slug' => '/page',
                    'request' => 'page',
                    'item' => 'web',
                    'count' => 'pages', // Название таблицы
                ],
                7 => [
                    'title' => 'Create',
                    'controller' => 'Page',
                    'parent_id' => 6,
                    'slug' => '/page/create',
                    'request' => 'page',
                    'item' => 'add',
                ],
                8 => [
                    'title' => 'Users',
                    'controller' => 'User',
                    'parent_id' => null,
                    'slug' => '/user',
                    'request' => 'user',
                    'item' => 'supervisor_account',
                    'count' => 'users', // Название таблицы
                ],
                9 => [
                    'title' => 'Create',
                    'controller' => 'User',
                    'parent_id' => 8,
                    'slug' => '/user/create',
                    'request' => 'user',
                    'item' => 'add',
                ],
            ];

        // Если выключена авторизация на сайте
        if (!config('add.auth')) {
            $menu = $menu + [
                    82 => [
                        'title' => 'Banned_ip',
                        'controller' => 'User',
                        'parent_id' => 8,
                        'slug' => '/user-banned-ip',
                        'request' => 'user',
                        'item' => 'remove_circle_outline',
                    ],
                ];
        }

        $menu = $menu + [
                10 => [
                    'title' => 'Menu',
                    'controller' => 'Menu',
                    'parent_id' => null,
                    'slug' => '/menu',
                    'request' => 'menu',
                    'item' => 'menu',
                    'add' => 12, // Добавляется Menu_name
                ],
                11 => [
                    'title' => 'Create_item',
                    'controller' => 'Menu',
                    'parent_id' => 10,
                    'slug' => '/menu/create',
                    'request' => 'menu',
                    'item' => 'add',
                ],
                12 => [
                    'title' => 'Menu_name',
                    'controller' => 'MenuName',
                    'parent_id' => 10,
                    'slug' => '/menu-name',
                    'request' => 'menu',
                    'item' => 'subject',
                    'add' => 10, // Добавляется Menu
                ],
                13 => [
                    'title' => 'Create_new_menu',
                    'controller' => 'MenuName',
                    'parent_id' => 12,
                    'slug' => '/menu-name/create',
                    'request' => 'menu',
                    'item' => 'add',
                ],
                16 => [
                    'title' => 'Additionally',
                    'controller' => 'Additionally',
                    'parent_id' => null,
                    'slug' => '/additionally',
                    'request' => 'additionally',
                    'item' => 'settings_ethernet',
                ],
                17 => [
                    'title' => 'Files',
                    'controller' => 'Additionally',
                    'parent_id' => 16,
                    'slug' => '/additionally/files',
                    'request' => 'additionally',
                    'item' => 'tab',
                ],
                18 => [
                    'title' => 'Logs',
                    'controller' => null,
                    'parent_id' => null,
                    'slug' => '/logs',
                    'request' => 'logs',
                    'item' => 'event_note',
                ],
                19 => [
                    'title' => 'ImportExport',
                    'controller' => 'ImportExport',
                    'parent_id' => null,
                    'slug' => '/import-export',
                    'request' => 'import-export',
                    'item' => 'import_export',
                ],
                14 => [
                    'title' => 'Settings',
                    'controller' => 'Setting',
                    'parent_id' => null,
                    'slug' => '/setting',
                    'request' => 'setting',
                    'item' => 'settings',
                ],
                15 => [
                    'title' => 'Create',
                    'controller' => 'Setting',
                    'parent_id' => 14,
                    'slug' => '/setting/create',
                    'request' => 'setting',
                    'item' => 'add',
                ],
                24 => [
                    'title' => 'Translate',
                    'controller' => 'Translate',
                    'parent_id' => null,
                    'slug' => '/translate',
                    'request' => 'translate',
                    'item' => 'translate',
                ],
                25 => [
                    'title' => 'Create',
                    'controller' => 'Translate',
                    'parent_id' => 24,
                    'slug' => '/translate/create',
                    'request' => 'translate',
                    'item' => 'add',
                ],
            ];

        return $menu;
    }
}