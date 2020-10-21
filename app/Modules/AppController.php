<?php

namespace App\Modules;

use App\Helpers\Breadcrumbs;
use App\Models\Main;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public $viewPath;
    public $lang;
    public $statusActive;
    public $perPage;
    public $breadcrumbs;
    public $userTable = 'users';
    public $userModel = '\App\Models\User';


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
        $statusActive = $this->statusActive = config('add.page_statuses')[1] ?: 'active';

        // Если нет данных, то выдадим ошибку
        if (!$modulesPath || !$modulesNamespace || !$viewPath || !$modulesLang) {
            Main::getError('Not data in Module', __METHOD__);
        }

        $this->perPage = config('add.pagination');
        $this->breadcrumbs = new Breadcrumbs();


        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        //view()->getFinder()->setPaths($modulesPath);


        // Только внутри этой конструкции работают некоторые методы
        /*$this->middleware(function ($request, $next) {

            $lang = lang();
            $authCheck = auth()->check();

            // Вручную аутентифицировать каждого пользователя как тестового
            if (!$authCheck) {
                $user = $this->userModel::find(1);
                auth()->login($user);
            }

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
        view()->share(compact('viewPath', 'statusActive', 'lang', 'searchQuery'));
    }
}
