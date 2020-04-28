<?php

namespace App\Modules\Shop\Controllers;

use App\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppController extends \App\Modules\AppController
{
    protected $module = 'Shop'; // Название модуля
    protected $m;
    protected $namespace;
    protected $modulePath;
    protected $viewPathModule;


    public function __construct(Request $request)
    {
        parent::__construct();

        $module = $this->module;
        $namespace = config('modules.namespace');
        $modulesPath = config('modules.path');
        if (!$module || !$namespace || !$modulesPath) {
            App::getError('Not data in Module', __METHOD__);
        }

        $this->namespace = "{$namespace}\\{$module}";
        $this->modulePath = "{$modulesPath}/{$module}";
        $m = $this->m = Str::lower($module);

        $model = $this->model = "{$this->namespace}\\Models\\{$module}";
        //$table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(1);
        $viewPathModule = $this->viewPathModule  = "{$module}.views";

        View::share(compact('module', 'm', 'model', 'route', 'viewPathModule')); // table
    }
}
