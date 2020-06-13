<?php


namespace App\Modules\Page\Controllers;

use App\Main;
use App\Modules\Admin\Helpers\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cookie;

class PostController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        //$view = $this->view = Str::snake($this->class);
        //Main::set('c', $c);
        //View::share(compact('class', 'c', 'view'));
    }


    // Записать куку через Ajax, после получения ответа перезагрузите страницу
    public function setCookie(Request $request)
    {
        if ($request->ajax()) {
            $name = $request->name ?? null;
            $value = $request->value ?? null;

            if ($name && $value) {

                // Ставим на очередь создание куки
                Cookie::queue($name, $value);
                return 1;
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
    }
}
