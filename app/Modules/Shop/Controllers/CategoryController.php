<?php

namespace App\Modules\Shop\Controllers;

use App\Main;
use App\Modules\Shop\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class CategoryController extends AppController
{
    // Кол-во товаров на странице каталог
    public $limit = 96;


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


    // Страница Каталог
    public function index(Request $request)
    {
        // Если нет вида
        Main::viewExists("{$this->viewPathModule}.{$this->c}_index", __METHOD__);

        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {
            $cat = $this->model::all();
            //$productsAll = Product::latest()->take($this->perPage)->get(); // Для фильтра
            $products = Product::latest()->take($this->perPage)->paginate($this->perPage);

        } else {
            $cat = $this->model::where('status', $this->statusActive)->get();
            //$productsAll = Product::where('status', $this->statusActive)->orderBy('id', 'desc')->take($this->limit)->get(); // Для фильтра
            $products = Product::where('status', $this->statusActive)->orderBy('id', 'desc')->take($this->limit)->paginate($this->perPage);
        }

        // Передаём в контейнер текущии товары
        //Main::set('products_now', $productsAll->toArray());

        $this->setMeta(__("{$this->lang}::sh.catalog"));
        return view("{$this->viewPathModule}.{$this->c}_index", compact('cat', 'products'));
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
            $values = $this->model::with('products')->where('slug', $slug)->first();
            $products = Product::with($this->c)->paginate($this->perPage);

        } else {
            $values = $this->model::with('products')->where('slug', $slug)->where('status', $this->statusActive)->first();
            $products = Product::with($this->c)->where('status', $this->statusActive)->paginate($this->perPage);
        }

        // Передаём в контейнер текущии товары
        //Main::set('products_now', $values->products->toArray());

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

        $this->setMeta($values->title ?? null, $values->description ?? null);
        return view("{$this->viewPathModule}.{$this->c}_show", compact('values', 'products'));
    }
}
