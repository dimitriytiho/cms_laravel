<?php


namespace App\Widgets\PanelDashboard;


use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PanelDashboard
{
    private $tpl;
    private $admin;
    private $toTemplate;


    private function __construct()
    {
        $this->tpl = __DIR__ . '/tpl/default.php';
        $this->admin = User::isAdminEditor();
        $this->toTemplate = $this->toTemplate();
    }


    public static function init()
    {
        if (Auth::check() && Auth::user()->Admin()) {
            $self = new self();

            // Сохраняем в сессию страницу с которой пользователь перешёл из админки
            $self->backLinkToAdmin();

            return $self->toTemplate;
        }
        return false;
    }


    // Сохраняем в сессию страницу с которой пользователь перешёл из админки
    private function backLinkToAdmin()
    {
        $backLink = url()->previous();
        $adminPrefix = config('add.admin');

        // Если url не содержит админский префикс
        $containAdmin = Str::is("*{$adminPrefix}*", $backLink);
        if ($containAdmin) {
            session()->put('back_link_admin', $backLink);
        }
    }


    private function toTemplate()
    {
        ob_start();
        include $this->tpl;
        return ob_get_clean();
    }
}
