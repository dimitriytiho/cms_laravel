<?php

namespace App\Modules;

use App\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AppController extends Controller
{
    protected $viewPath;
    protected $lang;
    protected $statusActive;


    public function __construct()
    {
        parent::__construct();

        $modulesPath = config('modules.path');
        $modulesNamespace = config('modules.namespace');
        $viewPath = $this->viewPath  = config('modules.views');
        $modulesLang = config('modules.lang');
        $this->statusActive = config('add.page_statuses')[1] ?: 'active';

        // Если нет данных, то выдадим ошибку
        if (!$modulesPath || !$modulesNamespace || !$viewPath || !$modulesLang) {
            App::getError('Not data in Module', __METHOD__);
        }

        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        View::getFinder()->setPaths($modulesPath);


        // Добавляем namespace для переводов
        $lang = $this->lang = "{$modulesNamespace}\\{$modulesLang}";

        // Использование переводов
        /*dump(__("{$this->lang}::a.Home"));
        dump(__("{$lang}::a.Home"));
        @lang("{$lang}::a.Home")*/

        // Строка поиска
        $searchQuery = s(request()->query('s')) ?: App::get('search_query');

        // Передаём в вид путь к видам
        View::share(compact('viewPath', 'lang', 'searchQuery'));
    }
}
