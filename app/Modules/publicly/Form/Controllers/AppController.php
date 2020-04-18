<?php

namespace App\Modules\publicly\Form\Controllers;

use App\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppController extends \App\Modules\publicly\AppController
{
    protected $module = 'Form'; // Название модуля
    protected $m;
    protected $namespace;
    protected $modulePath;


    public function __construct(Request $request)
    {
        parent::__construct();

        $module = $this->module;
        $namespace = config('modules.namespace');
        $modulesPath = config('modules.path');
        if (!$this->area || !$module || !$namespace || !$modulesPath) {
            App::getError('Not data in Module', __METHOD__);
        }

        $this->namespace = "{$namespace}\\{$this->area}\\{$module}";
        $this->modulePath = "{$modulesPath}/{$this->area}/{$module}";
        $m = $this->m = Str::lower($module);

        $model = $this->model = "{$this->namespace}\\Models\\{$module}";
        //$table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(1);
        $this->viewPath  = "{$module}.views";

        View::share(compact('module', 'm', 'model', 'route')); // table
    }
}
