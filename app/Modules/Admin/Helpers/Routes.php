<?php


namespace App\Modules\Admin\Helpers;

use App\Modules\Admin\Models\Setting;
use Illuminate\Support\Facades\File;

class Routes
{
    private $menu = null;

    private function __construct()
    {
        $this->menu = Setting::menuLeftAdmin();
    }


    /*
     * Возращает в массиве сегмент из меню по текущему роуту.
     * $slug - принимает $request->path() из запроса.
     */
    public static function currentRoute($slug)
    {
        $self = new self();
        $menu = $self->menu;
        $slug_now = ltrim($slug, env('APP_ADMIN'));
        $slug_now = $slug_now ?: '/';
        $slug_controller = '/' . \App\Helpers\Str::strToSegment($slug, 1);
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


    // При изменении меню из /config/admin.php запустить этот метод
    public static function routes()
    {
        self::makeAdminControllers();
        self::routesLaravel();
    }


    // Внимание, после создания необходимы правки! Если нет контроллеров для админки, то они создадуться. Рекомендуется использовать только при первом запуске админки.
    public static function makeAdminControllers()
    {
        $self = new self();
        $menu = $self->menu;
        $path = app_path() . '/Http/Controllers/Admin';

        if ($menu) {
            foreach ($menu as $v) {
                $name = "{$v['controller']}Controller";
                if (!is_file("$path/$name.php")) {
                    Commands::getCommand("make:controller Admin/$name --resource");
                }
            }
        }
    }


    // Внимание, после создания необходимы правки! Создание машрутов для админки в файл /routes/admin.php. Рекомендуется использовать только при первом запуске админки.
    public static function routesLaravel()
    {
        $self = new self();
        $menu = $self->menu;
        $part = "<?php\n\n";

        if ($menu) {
            foreach ($menu as $v) {
                if (!$v['parent_id'] && $v['slug'] !== '/') {
                    $part .= "Route::resource('{$v['slug']}', 'Admin\\{$v['controller']}Controller');\n";
                }
            }
            $file = base_path('routes/admin.php');
            if (File::exists(($file))) {
                File::replace($file, $part);
            }
        }
    }
}
