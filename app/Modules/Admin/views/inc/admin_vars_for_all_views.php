<?php

use App\Modules\Admin\Helpers\OnlineUsers;
use Illuminate\Support\Facades\View;
use App\Modules\Admin\Nav;
use Illuminate\Support\Facades\Request;
use App\Modules\Admin\Helpers\Routes;


$modulesPath = config('modules.path');
View::getFinder()->setPaths("{$modulesPath}/Admin/views");

$isAdmin = auth()->user()->isAdmin() ?? null;

$asideWidth = $_COOKIE['asideWidth'] ?? null;
$asideText = $asideWidth === config('admin.scss.aside-width-icon') ? ' style="display: none;"' : null;
$asideWidth = $asideWidth ? " style='width: $asideWidth;'" : null;

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

// Пользователи онлайн
$onlineUsers = null;
if (config('add.online_users')) {
    $onlineUsers = OnlineUsers::getUsers();
}
