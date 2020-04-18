<?php

namespace App\Http\Controllers\Shop;

use App\App;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Кол-во товаров на странице каталог
    public $limit = 96;


    public function __construct(Request $request)
    {
        parent::__construct();
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "\App\\{$this->class}";
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(1);
        $view = $this->view = Str::snake($this->class);
        App::set('c', $c);
        View::share(compact('class', 'c','model', 'table', 'route', 'view'));
    }


    // Страница Каталог
    public function index(Request $request)
    {
        // Если нет вида
        App::viewExists("{$this->view}.index", __METHOD__);

        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {
            $cat = $this->model::all();
            $products = Product::latest()->take($this->limit)->get();

        } else {
            $status = config('add.page_statuses')[1] ?: 'active';
            $cat = $this->model::where('status', $status)->get();
            $products = Product::where('status', $status)->orderBy('id', 'desc')->take($this->limit)->get();
        }

        $this->setMeta(__('sh.catalog'));
        return view("{$this->view}.index", compact('cat', 'products'));
    }


    public function show($slug)
    {
        // Если нет алиаса
        if (!$slug) {
            App::getError("{$this->class} not found or outdated", __METHOD__);
        }

        // Если нет вида
        App::viewExists("{$this->view}.show", __METHOD__);

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


        // Передаём id элемента
        App::set('id', $values->id);

        $this->setMeta($values->title ?? null, $values->description ?? null);
        return view("{$this->view}.show", compact('values'));
    }
}
