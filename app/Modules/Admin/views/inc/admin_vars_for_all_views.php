<?php

use Illuminate\Support\Facades\View;
use App\Modules\Admin\Nav;
use Illuminate\Support\Facades\Request;
use App\Modules\Admin\Helpers\Routes;


/*$modulesPath = config('modules.path');
View::getFinder()->setPaths("{$modulesPath}/Admin/views");*/

$admin = config('modules.admin');
$viewPath = "{$admin}.views";

$isAdmin = auth()->check() ? auth()->user()->isAdmin() : null;
$adminLimited = auth()->check() ? auth()->user()->adminLimited() : null;

// Левое меню для мобильных
$menuAsideChunk = null;
$menuAside = Nav::menuLeft();
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

$lang = lang();
$currentRoute = Routes::currentRoute(Request::path()) ?? null;
$currentRoutesExclude = Routes::currentRoutes($currentRoute) ?? null;
