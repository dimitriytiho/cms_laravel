<?php

namespace App\Modules\Admin\Controllers;

use App\Main;
use App\Modules\Admin\Helpers\Locale;
use App\Modules\Admin\Helpers\OnlineUsers;
use App\Modules\Admin\Helpers\Routes;
use App\Modules\Admin\Nav;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    protected $dbSort;

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
    protected $adminLimited;
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
        $modulesPath = $this->modulePath = "{$modulesPath}/{$this->module}";

        $constructor = $this->constructor = "{$namespaceHelpers}\\Constructor";
        $dbSort = $this->dbSort = "{$namespaceHelpers}\\DbSort";
        $this->perPage = config('admin.settings.pagination');

        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        $viewPath = $this->viewPath  = 'views';
        view()->getFinder()->setPaths("{$modulesPath}/{$this->viewPath}");


        // Добавляем namespace для переводов
        $lang = $this->lang = "{$this->namespace}\\{$modulesLang}";


        $currentRoute = $this->currentRoute = Routes::currentRoute($request->path());
        $controller = $this->controller = $currentRoute['controller'] ?? null;
        $c = $this->c = strtolower($controller);

        // Только внутри этой конструкции работают некоторые методы
        $this->middleware(function ($request, $next) {

            // Устанавливаем локаль
            Locale::setLocaleFromCookie($request);

            $this->user = auth()->check() ? auth()->user() : null;
            $this->isAdmin = $isAdmin = auth()->check() ? auth()->user()->isAdmin() : null;
            $this->adminLimited = $adminLimited = $this->user ? auth()->user()->adminLimited() : null;

            // Если Редактор откроет запрещённый раздел, выбросится исключение
            if (in_array($this->controller, config('admin.editor_section_banned')) && !$isAdmin) {
                Main::getError('Editor section Banned!', __METHOD__);
            }

            // Если Кассир откроет запрещённый раздел, выбросится исключение
            if (!in_array($this->controller, config('admin.cashier_section_allow')) && !$isAdmin) {
                Main::getError('Cashier section Banned!', __METHOD__);
            }

            view()->share(compact('isAdmin', 'adminLimited'));


            // Сохраняем в сессию страницу с которой пользователь перешёл в админку
            $backLink = url()->previous();
            $adminPrefix = config('add.admin');
            // Если url не содержит админский префикс
            $containAdmin = Str::is("*{$adminPrefix}*", $backLink);
            $enter = config('add.enter');
            $containEnter = Str::is("*{$enter}*", $backLink);
            if (!($containAdmin || $containEnter)) {
                session()->put('back_link_site', $backLink);
            }


            return $next($request);
        });

        //$breadcrumbs = Breadcrumbs::breadcrumbs(Nav::menuLeft(), $currentRoute);
        $currentRoutesExclude = Routes::currentRoutes($currentRoute);
        $table = $this->table = null;
        $this->template = 'general';

        $asideWidth = $request->cookie('asideWidth');
        //$asideText = $asideWidth === config('admin.scss.aside-width-icon') ? ' style="display: none;"' : null;

        // Левое меню для мобильных
        $menuAsideChunk = null;
        $menuAside = Nav::menuLeft() ?? [];
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
        $onlineUsers = [];
        if (config('add.online_users')) {
            $onlineUsers = OnlineUsers::getUsers();
        }

        view()->share(compact('viewPath', 'lang', 'currentRoute', 'controller', 'c', 'table', 'currentRoutesExclude', 'asideWidth', 'menuAsideChunk', 'menuAside', 'imgRequestName', 'imgUploadID', 'namespaceHelpers', 'modulesPath', 'constructor', 'dbSort', 'onlineUsers'));
    }
}
