<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Main;

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
