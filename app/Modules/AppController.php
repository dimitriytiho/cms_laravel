<?php

namespace App\Modules;

use App\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppController extends Controller
{
    protected $viewPath;


    public function __construct()
    {
        parent::__construct();

        $modulesPath = config('modules.path');
        $viewPath = $this->viewPath  = config('modules.views');

        // Если нет данных, то выдадим ошибку
        if (!$modulesPath || !$viewPath) {
            App::getError('Not data in Module', __METHOD__);
        }

        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        View::getFinder()->setPaths($modulesPath);

        // Передаём в вид путь к видам
        View::share(compact('viewPath'));
    }
}
