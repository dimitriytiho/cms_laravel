<?php


namespace App\Widgets\PanelDashboard;


use App\User;
use Illuminate\Support\Facades\Auth;

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


    public static function run()
    {
        if (Auth::check() && Auth::user()->Admin()) {
            $self = new self();
            return $self->toTemplate;
        }
        return false;
    }


    private function toTemplate()
    {
        ob_start();
        include $this->tpl;
        return ob_get_clean();
    }
}
