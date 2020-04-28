<?php

use Illuminate\Support\Facades\View;
use App\Modules\Admin\Models\Setting;
use Illuminate\Support\Facades\Request;
use App\Modules\Admin\Helpers\Routes;


$modulesPath = config('modules.path');
View::getFinder()->setPaths("{$modulesPath}/Admin/views");

$isAdmin = auth()->user()->isAdmin() ?? null;

$asideWidth = $_COOKIE['asideWidth'] ?? null;
$asideText = $asideWidth === config('add.scss-admin.aside-width-icon') ? ' style="display: none;"' : null;
$asideWidth = $asideWidth ? " style='width: $asideWidth;'" : null;

// Левое меню для мобильных
$menuAsideChunk = null;
$menuAside = Setting::menuLeftAdmin();
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


$currentRoute = Routes::currentRoute(Request::path()) ?? null;
$currentRoutesExclude = Routes::currentRoutes($currentRoute) ?? null;
