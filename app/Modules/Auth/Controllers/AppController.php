<?php

namespace App\Modules\Auth\Controllers;

use App\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppController extends \App\Modules\AppController
{
    protected $module = 'Auth'; // Название модуля
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
        $route = $this->route = $request->segment(1);
        $viewPathModule = $this->viewPathModule  = "{$module}.views";

        View::share(compact('module', 'm', 'model', 'route', 'viewPathModule'));
    }


    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }


    public function username()
    {
        return 'email';
    }


    protected function redirectPath()
    {
        return route('index');
    }
}
