<?php

namespace App\Modules\publicly;

use App\App;
use App\Helpers\Upload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppController extends Controller
{
    protected $area = 'AREA_PUBLIC'; // Название области видимости из env файла
    protected $viewPath;


    public function __construct()
    {
        parent::__construct();

        $this->area = env($this->area);
        $modulesPath = config('modules.path');

        // Если нет данных, то выдадим ошибку
        if (!$this->area || !$modulesPath) {
            App::getError('Not data in Module', __METHOD__);
        }

        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        $viewPath = $this->viewPath  = 'views';
        View::getFinder()->setPaths("{$modulesPath}/{$this->area}");

        // Передаём в вид путь к видам
        View::share(compact('viewPath'));
    }
}
