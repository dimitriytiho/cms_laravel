<?php

namespace App\Http\Controllers;

use App\Main;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $class;
    protected $c;
    protected $model;
    protected $table;
    protected $route;
    protected $view;



    public function __construct()
    {

    }


    // Метод вывода мета тегов в head
    protected function setMeta($title, $description = '')
    {
        Main::setMeta($title, $description);
    }
}
