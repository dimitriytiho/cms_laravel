<?php


namespace App\Modules\Admin\Helpers;

use App\Modules\Admin\Nav;
use App\Helpers\Str as helpersStr;

class Routes
{
    private $menu;


    private function __construct()
    {
        $this->menu = Nav::menuLeft();
    }


    /*
     * Возращает в массиве сегмент из меню по текущему роуту.
     * $slug - принимает $request->path() из запроса.
     */
    public static function currentRoute($slug)
    {
        $self = new self();
        $menu = $self->menu;
        $slug_now = ltrim($slug, config('add.admin', 'dashboard'));
        $slug_now = $slug_now ?: '/';
        $slug_controller = '/' . helpersStr::strToSegment($slug, 1);
        $int = (int)class_basename($slug);

        if ($menu && $slug) {
            foreach ($menu as $k => $v) {

                if ($slug_now === $v['slug']) {
                    $v['id'] = $k;
                    return $v;

                } elseif ($v['slug'] === $slug_controller && class_basename($slug) === 'edit') {
                    return [
                        'id' => $k,
                        'title' => 'edit',
                        'controller' => $v['controller'],
                        'parent_id' => $v['parent_id'],
                        'slug' => $slug_now,
                    ];

                } elseif ($v['slug'] === $slug_controller && $int && is_int($int)) {
                    return [
                        'id' => $k,
                        'title' => 'view',
                        'controller' => $v['controller'],
                        'parent_id' => $v['parent_id'],
                        'slug' => $slug_now,
                    ];
                }
            }
        }
        return false;
    }


    /*
     * Возращает массив сегментов из меню по текущему контроллеру.
     * $currentRoute - принимает результат метода currentRoute (текущий роутер).
     * $exclude - передайте true, чтобы исключить текущий роут.
     */
    public static function currentRoutes($currentRoute, $exclude = false)
    {
        $self = new self();
        $menu = $self->menu;
        $part = [];

        if ($menu && $currentRoute) {
            foreach ($menu as $k => $v) {
                if ($currentRoute['id'] === $k && isset($v['add'])) {
                    $part[] = $menu[$v['add']];
                }

                if ($currentRoute['controller'] === $v['controller']) {
                    if ($currentRoute['slug'] === '/') {
                        continue;
                    }

                    if ($exclude && $currentRoute['slug'] === $v['slug'] || $exclude && $currentRoute['slug'] === '/') {
                        continue;
                    }
                    $part[] = $v;
                }
            }
            return $part;
        }
        return false;
    }
}
