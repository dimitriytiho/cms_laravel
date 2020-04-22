<?php

namespace App\Modules\Page\Controllers;

use App\App;
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
        App::set('c', $c);
        View::share(compact('class', 'c'));
    }


    public function index()
    {
        //dump(session()->all());

        //Mail::to('dimitriyyuliya@gmail.com')->send(new SendMail(__('a.Code'), '12345'));

        /*$mobileDetect = new \Mobile_Detect();
        dump($mobileDetect->isMobile());
        dump($mobileDetect->isTablet());*/


        App::viewExists("{$this->viewPath}.{$this->c}_index", __METHOD__);
        $this->setMeta(__('c.home'), __('c.You_are_on_home'));
        return view("{$this->viewPath}.{$this->c}_index");
    }


    public function show($slug)
    {
        // Если нет алиаса
        if (!$slug) {
            App::getError("{$this->class} not found or outdated", __METHOD__);
        }

        // Если нет вида
        App::viewExists("{$this->viewPath}.{$this->c}_show", __METHOD__);

        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {
            $values = $this->model::where('slug', $slug)->first();

        } else {
            $status = config('add.page_statuses')[1] ?: 'active';
            $values = $this->model::where('slug', $slug)->where('status', $status)->first();
        }

        // Если нет страницы
        if (!$values) {
            App::getError("{$this->class} not found", __METHOD__);
        }

        /*
         * Если есть подключаемые файлы (текст в контенте ##!!!inc_name, а сам файл в /resources/views/inc), то они автоматически подключатся.
         * Если нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, в подключаемом файле.
         * Дополнительно, в этот файл передаются данные страницы $values.
         */
        $values->body = App::inc($values->body, $values);

        // Использовать скрипты в контенте, они будут перенесены вниз страницы.
        $values->body = App::getDownScript($values->body);


        // Передаём в контейнер id элемента
        App::set('id', $values->id);

        $this->setMeta($values->title ?? null, $values->description ?? null);
        return view("{$this->viewPath}.{$this->c}_show", compact('values'));
    }


    public function contactUs(Request $request)
    {
        App::viewExists("{$this->viewPath}.{$this->c}_contact_us", __METHOD__);
        $this->setMeta(__('c.contact_us'));
        return view("{$this->viewPath}.{$this->c}_contact_us");
    }


    public function notFound()
    {
        App::viewExists('views.errors.404', __METHOD__);

        $title = __('s.page_not_found');
        $message = __('s.whoops_no_page');

        App::getError($title, __METHOD__, null, 'info');
        $this->setMeta($title);
        return response()->view('views.errors.404', compact('title', 'message'), 404);
    }
}
