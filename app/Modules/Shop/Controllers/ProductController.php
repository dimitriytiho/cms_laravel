<?php

namespace App\Modules\Shop\Controllers;

use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ProductController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "$this->namespace\\Models\\{$this->class}";
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(1);
        $view = $this->view = Str::snake($this->class);
        Main::set('c', $c);
        View::share(compact('class', 'c','model', 'table', 'route', 'view'));
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
         * Если есть подключаемые файлы (текст в контенте ##!!!inc_name, а сам файл в /resources/views/inc), то они автоматически подключатся.
         * Если нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, в подключаемом файле.
         * Дополнительно, в этот файл передаются данные страницы $values.
         */
        $values->body = Main::inc($values->body, $values);

        // Использовать скрипты в контенте, они будут перенесены вниз страницы.
        $values->body = Main::getDownScript($values->body);


        // Передаём id элемента
        Main::set('id', $values->id);

        // Хлебные крошки
        $categoryId = $values->category[0]->id ?? null;
        $breadcrumbs = $this->breadcrumbs
            ->values('categories')
            ->end([
                route($this->route, $values->slug) => $values->title
            ])
            ->dynamic($categoryId, 'category')
            ->add([[route('catalog') => 'catalog']])
            ->get();

        $this->setMeta($values->title ?? null, $values->description ?? null);
        return view("{$this->viewPathModule}.{$this->c}_show", compact('values', 'breadcrumbs'));
    }
}
