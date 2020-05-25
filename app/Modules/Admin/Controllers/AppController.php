<?php

namespace App\Modules\Admin\Controllers;

use App\Main;
use App\Modules\Admin\Helpers\Locale;
use App\Modules\Admin\Helpers\OnlineUsers;
use App\Modules\Admin\Helpers\Routes;
use App\Modules\Admin\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppController extends Controller
{
    protected $module = 'Admin'; // Название модуля
    protected $m;
    protected $viewPath;
    protected $namespace;
    protected $namespaceHelpers;
    protected $modulePath;
    protected $constructor;

    protected $currentRoute;
    protected $controller;
    protected $c;
    protected $table;
    protected $template;
    protected $class;
    protected $route;
    protected $model;
    protected $view;
    protected $lang;

    protected $user;
    protected $isAdmin;
    protected $imgRequestName;
    protected $imgUploadID;

    protected $perPage;


    public function __construct(Request $request)
    {
        parent::__construct();

        $modulesPath = config('modules.path');
        $this->namespace = config('modules.namespace');
        $modulesLang = config('modules.lang');

        // Если нет данных, то выдадим ошибку
        if (!$this->namespace || !$this->module || !$modulesPath || !$modulesLang) {
            Main::getError('Not data in Module', __METHOD__);
        }

        $this->m = Str::lower($this->module);
        $namespaceHelpers = $this->namespaceHelpers = "{$this->namespace}\\{$this->module}\\Helpers";
        $constructor = $this->constructor = "{$namespaceHelpers}\\Constructor";
        $modulesPath = $this->modulePath = "{$modulesPath}/{$this->module}";

        $this->perPage = config('admin.settings.pagination');

        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        $viewPath = $this->viewPath  = 'views';
        View::getFinder()->setPaths("{$modulesPath}/{$this->viewPath}");


        // Добавляем namespace для переводов
        $lang = $this->lang = "{$this->namespace}\\{$modulesLang}";


        Locale::setLocaleFromCookie($request);
        $currentRoute = $this->currentRoute = Routes::currentRoute($request->path());
        $controller = $this->controller = $currentRoute['controller'] ?? null;
        $c = $this->c = strtolower($controller);

        // Конструкция для получения auth() через middleware, auth() работает внутри этой конструкции
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user() ?? null;
            $this->isAdmin = $isAdmin = auth()->user()->isAdmin() ?? null;

            // Если Редактор откроет запрещённый раздел, выбросится исключение
            if (in_array($this->controller, config('admin.editor_section_banned')) && !$isAdmin) {
                Main::getError('Editor section Banned!', __METHOD__);
            }

            View::share(compact('isAdmin'));

            return $next($request);
        });

        //$breadcrumbs = Breadcrumbs::breadcrumbs(Setting::menuLeftAdmin(), $currentRoute);
        $currentRoutesExclude = Routes::currentRoutes($currentRoute);
        $table = $this->table = null;
        $this->template = 'general';

        $asideWidth = $request->cookie('asideWidth');
        $asideText = $asideWidth === config('add.scss-admin.aside-width-icon') ? ' style="display: none;"' : null;

        // Левое меню для мобильных
        $menuAsideChunk = null;
        $menuAside = Setting::menuLeftAdmin() ?? [];
        if ($menuAside && is_array($menuAside) && count($menuAside) > 2) {

            $menuAsideOnlyParent = [];
            foreach ($menuAside as $elMenu) {
                if (!$elMenu['parent_id']) {
                    $menuAsideOnlyParent[] = $elMenu;
                }
            }

            if ($menuAsideOnlyParent) {
                $menuAsideCount = (int)ceil(count($menuAsideOnlyParent) / 2);
                $menuAsideChunk = array_chunk($menuAsideOnlyParent, $menuAsideCount);
            }

        }

        // Переменные для Dropzone JS
        $imgRequestName = $this->imgRequestName = null;
        $imgUploadID = $this->imgUploadID = null;

        // Пользователи онлайн
        $onlineUsers = null;
        if (config('add.online_users')) {
            $onlineUsers = OnlineUsers::getUsers();
        }

        View::share(compact('viewPath', 'lang', 'currentRoute', 'controller', 'c', 'table', 'currentRoutesExclude', 'asideWidth', 'asideText', 'menuAsideChunk', 'menuAside', 'imgRequestName', 'imgUploadID', 'namespaceHelpers', 'modulesPath', 'constructor', 'onlineUsers'));
    }
}
