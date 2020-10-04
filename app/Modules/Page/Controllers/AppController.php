<?php

namespace App\Modules\Page\Controllers;

use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class AppController extends \App\Modules\AppController
{
    protected $module = 'Page'; // Название модуля
    protected $m;
    protected $namespace;
    protected $modulePath;
    protected $viewPathModule;


    public function __construct(Request $request)
    {
        parent::__construct();

        $route = null; // Задайте название маршрута, если он называется не как модуль
        $route = $this->route = Route::has(Str::snake($this->module)) ? Str::snake($this->module) : 'page';

        $module = $this->module;
        $namespace = config('modules.namespace');
        $modulesPath = config('modules.path');
        if (!$module || !$namespace || !$modulesPath) {
            Main::getError('Not data in Module', __METHOD__);
        }

        $this->namespace = "{$namespace}\\{$module}";
        $this->modulePath = "{$modulesPath}/{$module}";
        $m = $this->m = Str::lower($module);

        $model = $this->model = "{$this->namespace}\\Models\\{$module}";
        $table = $this->table = with(new $model)->getTable();
        $viewPathModule = $this->viewPathModule  = "{$module}.views";

        View::share(compact('module', 'm', 'table', 'model', 'route', 'viewPathModule'));
    }
}
