<?php

namespace App\Modules;

use App\Main;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AppController extends Controller
{
    public $viewPath;
    public $lang;
    public $statusActive;
    public $perPage;
    public $userTable = 'users';
    public $userModel = '\App\User';


    public function __construct()
    {
        parent::__construct();

        // Пользователи онлайн
        if (config('add.online_users')) {
            $this->middleware('online-users');
        }

        $modulesPath = config('modules.path');
        $modulesNamespace = config('modules.namespace');
        $viewPath = $this->viewPath  = config('modules.views');
        $modulesLang = config('modules.lang');
        $this->statusActive = config('add.page_statuses')[1] ?: 'active';

        // Если нет данных, то выдадим ошибку
        if (!$modulesPath || !$modulesNamespace || !$viewPath || !$modulesLang) {
            Main::getError('Not data in Module', __METHOD__);
        }

        $this->perPage = config('add.pagination');

        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        View::getFinder()->setPaths($modulesPath);


        // Конструкция для получения auth() через middleware, auth() работает внутри этой конструкции
        /*$this->middleware(function ($request, $next) {

            $lang = lang();
            $authCheck = auth()->check();

            //View::share(compact('authCheck'));
            return $next($request);
        });*/


        // Добавляем namespace для переводов
        $lang = $this->lang = "{$modulesNamespace}\\{$modulesLang}";

        // Использование переводов
        /*dump(__("{$this->lang}::a.Home"));
        dump(__("{$lang}::a.Home"));
        @lang("{$lang}::a.Home")*/

        //cache()->flush(); // Удалить все кэши

        // Строка поиска
        $searchQuery = s(request()->query('s')) ?: Main::get('search_query');

        // Передаём в вид путь к видам
        View::share(compact('viewPath', 'lang', 'searchQuery'));
    }
}
