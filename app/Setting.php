<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id'];


    // Возвращает массив названий настроек, название которых нельзя изменить из панели управления
    public static function titleNoEditArr() {
        return [
            'site_name',
            'admin_email',
            'site_email',
            'tel',
            'date_format',
            'change_key',
            'banned_ip_count',
        ];
    }


    // ОСНОВНОЕ ЛЕВОЕ МЕНЮ ADMIN
    // Не менять нумерацию! Если надо можно переставить местами и добавить другие цифры ключей.
    public static function menuLeftAdmin() {
        $menu = [];
        $menu = $menu + [
                1 => [
                    'title' => 'Dashboard',
                    'controller' => 'Main',
                    'parent_id' => null,
                    'slug' => '/',
                    'item' => 'dashboard',
                ],
            ];

        // Если включен shop
        if (env('APP_SHOP', null)) {
            $menu = $menu + [
                2 => [
                    'title' => 'Orders',
                    'controller' => 'Order',
                    'parent_id' => null,
                    'slug' => '/order',
                    'item' => 'shopping_cart',
                ],
            ];
        }

        $menu = $menu + [
                3 => [
                    'title' => 'Forms',
                    'controller' => 'Form',
                    'parent_id' => null,
                    'slug' => '/form',
                    'item' => 'insert_comment',
                ],
            ];

        // Если включен shop
        if (env('APP_SHOP', null)) {
            $menu = $menu + [
                4 => [
                    'title' => 'Categories',
                    'controller' => 'Category',
                    'parent_id' => null,
                    'slug' => '/category',
                    'item' => 'account_tree',
                ],
                41 => [
                    'title' => 'Create',
                    'controller' => 'Category',
                    'parent_id' => 4,
                    'slug' => '/category/create',
                    'item' => 'add',
                ],
                5 => [
                    'title' => 'Products',
                    'controller' => 'Product',
                    'parent_id' => null,
                    'slug' => '/product',
                    'item' => 'all_inbox',
                ],
                51 => [
                    'title' => 'Create',
                    'controller' => 'Product',
                    'parent_id' => 5,
                    'slug' => '/product/create',
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
                'item' => 'web',
            ],
            7 => [
                'title' => 'Create',
                'controller' => 'Page',
                'parent_id' => 6,
                'slug' => '/page/create',
                'item' => 'add',
            ],
            8 => [
                'title' => 'Users',
                'controller' => 'User',
                'parent_id' => null,
                'slug' => '/user',
                'item' => 'supervisor_account',
            ],
            9 => [
                'title' => 'Create',
                'controller' => 'User',
                'parent_id' => 8,
                'slug' => '/user/create',
                'item' => 'add',
            ],
            82 => [
                'title' => 'Banned_ip',
                'controller' => 'User',
                'parent_id' => 8,
                'slug' => '/user-banned-ip',
                'item' => 'remove_circle_outline',
            ],
            10 => [
                'title' => 'Menu',
                'controller' => 'Menu',
                'parent_id' => null,
                'slug' => '/menu',
                'item' => 'menu',
                'add' => 12, // Добавляется Menu_name
            ],
            11 => [
                'title' => 'Create_item',
                'controller' => 'Menu',
                'parent_id' => 10,
                'slug' => '/menu/create',
                'item' => 'add',
            ],
            12 => [
                'title' => 'Menu_name',
                'controller' => 'MenuName',
                'parent_id' => 10,
                'slug' => '/menu-name',
                'item' => 'subject',
                'add' => 10, // Добавляется Menu
            ],
            13 => [
                'title' => 'Create_new_menu',
                'controller' => 'MenuName',
                'parent_id' => 12,
                'slug' => '/menu-name/create',
                'item' => 'add',
            ],
            14 => [
                'title' => 'Settings',
                'controller' => 'Setting',
                'parent_id' => null,
                'slug' => '/setting',
                'item' => 'tune',
            ],
            15 => [
                'title' => 'Create',
                'controller' => 'Setting',
                'parent_id' => 14,
                'slug' => '/setting/create',
                'item' => 'add',
            ],
            16 => [
                'title' => 'Additionally',
                'controller' => 'Additionally',
                'parent_id' => null,
                'slug' => '/additionally',
                'item' => 'settings_ethernet',
            ],
            17 => [
                'title' => 'Files',
                'controller' => 'Additionally',
                'parent_id' => 16,
                'slug' => '/additionally/files',
                'item' => 'tab',
            ],
            18 => [
                'title' => 'Logs',
                'controller' => null,
                'parent_id' => null,
                'slug' => '/logs',
                'item' => 'list',
            ],
            19 => [
                'title' => 'ImportExport',
                'controller' => 'ImportExport',
                'parent_id' => null,
                'slug' => '/import-export',
                'item' => 'import_export',
            ],
        ];

        return $menu;
    }
}
