<?php

namespace App\Http\Controllers\Admin;

use App\App;
use App\Helpers\Admin\Locale;
use App\Helpers\Admin\Routes;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class AppController extends Controller
{
    protected $currentRoute;
    protected $controller;
    protected $c;
    protected $table;
    protected $template;
    protected $class;
    protected $route;
    protected $model;
    protected $view;

    protected $user;
    protected $isAdmin;
    protected $imgRequestName;
    protected $imgUploadID;


    public function __construct(Request $request)
    {
        parent::__construct();

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
                App::getError('Editor section BANNED!', __METHOD__);
            }

            View::share(compact('isAdmin'));

            return $next($request);
        });

        //$breadcrumbs = Breadcrumbs::breadcrumbs(Setting::menuLeftAdmin(), $currentRoute);
        $currentRoutesExclude = Routes::currentRoutes($currentRoute);
        $table = $this->table = null;
        $this->template = 'general';

        $asideWidth = $_COOKIE['asideWidth'] ?? null;
        $asideText = $asideWidth === config('add.scss-admin.aside-width-icon') ? ' style="display: none;"' : null;
        $asideWidth = $asideWidth ? " style='width: $asideWidth;'" : null;

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

        View::share(compact('currentRoute', 'controller', 'c', 'table', 'currentRoutesExclude', 'asideWidth', 'asideText', 'menuAsideChunk', 'menuAside', 'imgRequestName', 'imgUploadID'));
    }
}
