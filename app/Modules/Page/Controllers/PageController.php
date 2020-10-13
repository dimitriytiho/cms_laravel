<?php

namespace App\Modules\Page\Controllers;

use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class PageController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        //$view = $this->view = Str::snake($this->class);
        Main::set('c', $c);
        View::share(compact('class', 'c'));
    }


    public function index()
    {
        //dump(session()->all());

        //Mail::to('dimitriyyuliya@gmail.com')->send(new SendMail(__("{$this->lang}::a.Code"), '12345'));

        /*$mobileDetect = new \Mobile_Detect();
        dump($mobileDetect->isMobile());
        dump($mobileDetect->isTablet());*/



        Main::viewExists("{$this->viewPathModule}.{$this->c}_index", __METHOD__);
        $title = __("{$this->lang}::s." . config('add.title_main'));

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->get();

        $this->setMeta($title, __("{$this->lang}::s.You_are_on_home"));
        return view("{$this->viewPathModule}.{$this->c}_index", compact('breadcrumbs'));
    }


    public function show($slug)
    {
        // Если нет алиаса
        if (!$slug) {
            Main::getError("{$this->class} not found or outdated", __METHOD__);
        }

        // Если нет вида
        Main::viewExists("{$this->viewPathModule}.{$this->c}_show", __METHOD__);

        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {
            $values = $this->model::where('slug', $slug)->first();

        } else {
            $values = $this->model::where('slug', $slug)->where('status', $this->statusActive)->first();
        }

        // Если нет страницы
        if (!$values) {
            Main::getError("{$this->class} not found", __METHOD__);
        }

        /*
         * Если есть подключаемые файлы (текст в контенте ##!!!inc_name, а сам файл в /app/Modules/views/inc), то они автоматически подключатся.
         * Если нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, в подключаемом файле.
         * Дополнительно, в этот файл передаются данные страницы $values.
         */
        $values->body = Main::inc($values->body, $values);

        // Использовать скрипты в контенте, они будут перенесены вниз страницы.
        $values->body = Main::getDownScript($values->body);


        // Передаём в контейнер id элемента
        Main::set('id', $values->id);

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->values($this->table)
            ->dynamic($values->id)
            ->get();

        $this->setMeta($values->title ?? null, $values->description ?? null);
        return view("{$this->viewPathModule}.{$this->c}_show", compact('values', 'breadcrumbs'));
    }


    public function contactUs(Request $request)
    {
        Main::viewExists("{$this->viewPathModule}.{$this->c}_contact_us", __METHOD__);
        $title = __("{$this->lang}::s.contact_us");

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->end(['contact_us' => $title])
            ->get();

        $this->setMeta($title);
        return view("{$this->viewPathModule}.{$this->c}_contact_us", compact('breadcrumbs'));
    }


    public function notFound()
    {
        Main::viewExists("{$this->viewPath}.errors.404", __METHOD__);

        $title = __("{$this->lang}::s.page_not_found");
        $message = __("{$this->lang}::s.whoops_no_page");

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->end(['not_found' => $title])
            ->get();

        //Main::getError($title, __METHOD__, null, 'info');
        $this->setMeta($title);
        return response()->view("{$this->viewPath}.errors.404", compact('title', 'message', 'breadcrumbs'), 404);
    }
}
