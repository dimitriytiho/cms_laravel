<?php

namespace App\Modules\Shop\Controllers;

use App\Models\Main;
use App\Modules\Shop\Helpers\Filter;
use App\Modules\Shop\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
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


        // Обработчик для фильтров
        $filter = Filter::getFilter();
        $countFilterGroup = Filter::getCountGroups($filter);
        $havingCount = $countFilterGroup > 1 ? "GROUP BY product_id HAVING COUNT(product_id) = {$countFilterGroup}" : null;

        // Или (т.е. будут показывать все товары, которые выбрали)
        $part = $filter ? "id IN (SELECT product_id FROM filter_products WHERE filter_value_id IN ({$filter}))" : null;


        // И (т.е. покажем пересечённые товары)
        //$part = $filter ? "id IN (SELECT product_id FROM filter_products WHERE filter_value_id IN ({$filter}) GROUP BY product_id HAVING COUNT(product_id) = {$countFilterGroup})" : null;

        // И, группируем по группам (т.е. внутри группы или - все на которые кликнули, а если кликнут на другую группу, то покажем пересечённые товары)
        //$part = $filter ? "id IN (SELECT product_id FROM filter_products WHERE filter_value_id IN ({$filter}) {$havingCount})" : null;


        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {

            $part = $part ? "WHERE $part" : null;
            $productsArr = DB::select("SELECT * FROM products $part ORDER BY sort DESC, id DESC LIMIT {$this->limit}");

            //$values = $this->model::all();
            //$productsAll = Product::latest()->take($this->perPage)->get(); // Для фильтра
            //$products = Product::latest()->take($this->perPage)->paginate($this->perPage);

        } else {

            $part = $part ? "AND $part" : null;
            $productsArr = DB::select("SELECT * FROM products WHERE status = :status $part ORDER BY sort DESC, id DESC LIMIT {$this->limit}",
                ['status' => $this->statusActive]);

            //$values = $this->model::where('status', $this->statusActive)->get();
            //$productsAll = Product::where('status', $this->statusActive)->orderBy('id', 'desc')->take($this->limit)->get(); // Для фильтра
            //$productsArr = Product::where('status', $this->statusActive)->orderBy('id', 'desc')->take($this->limit)->paginate($this->perPage);
        }

        // Пагинация из объекта
        $productsArr = collect($productsArr);
        $currentPage = Paginator::resolveCurrentPage();
        $currentPageItems = $productsArr->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
        $products = new Paginator($currentPageItems, count($productsArr), $this->perPage);
        $products->setPath($request->url());
        $products->appends($request->all());

        // Если ajax, то отдадим вид для фильтров
        if ($request->ajax()) {
            return view("{$this->viewPathModule}.filter", compact('products'))->render();
        }

        $title = __("{$this->lang}::s.catalog");

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->end(['catalog' => $title])
            ->get();

        $this->setMeta($title);
        return view("{$this->viewPathModule}.{$this->c}_index", compact('products', 'breadcrumbs'));
    }


    public function show($slug, Request $request)
    {
        // Если нет алиаса
        if (!$slug) {
            Main::getError("{$this->class} not found or outdated", __METHOD__);
        }

        // Если нет вида
        Main::viewExists("{$this->viewPathModule}.{$this->c}_show", __METHOD__);


        // Обработчик для фильтров
        $filter = Filter::getFilter();
        $countFilterGroup = Filter::getCountGroups($filter);
        $havingCount = $countFilterGroup > 1 ? "GROUP BY product_id HAVING COUNT(product_id) = {$countFilterGroup}" : null;

        // Или (т.е. будут показывать все товары, которые выбрали)
        $part = $filter ? "AND id IN (SELECT product_id FROM filter_products WHERE filter_value_id IN ({$filter}))" : null;

        // И (т.е. покажем пересечённые товары)
        //$part = $filter ? "AND id IN (SELECT product_id FROM filter_products WHERE filter_value_id IN ({$filter}) GROUP BY product_id HAVING COUNT(product_id) = {$countFilterGroup})" : null;

        // И, группируем по группам (т.е. внутри группы или - все на которые кликнули, а если кликнут на другую группу, то покажем пересечённые товары)
        //$part = $filter ? "AND id IN (SELECT product_id FROM filter_products WHERE filter_value_id IN ({$filter}) {$havingCount})" : null;

        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {
            $values = $this->model::with('products')->where('slug', $slug)->first();

            $productsArr = DB::select("SELECT * FROM products WHERE id IN (SELECT product_id FROM category_product WHERE category_id = :category_id) $part ORDER BY sort DESC, id DESC LIMIT {$this->limit}", ['category_id' => $values->id]);

            //$products = Product::with($this->c)->paginate($this->perPage);

        } else {

            $values = $this->model::with('products')->where('slug', $slug)->where('status', $this->statusActive)->first();

            $productsArr = DB::select("SELECT * FROM products WHERE id IN (SELECT product_id FROM category_product WHERE category_id = :category_id) AND status = :status $part ORDER BY sort DESC, id DESC LIMIT {$this->limit}", ['category_id' => $values->id, 'status' => $this->statusActive]);

            //$products = Product::with($this->c)->where('status', $this->statusActive)->paginate($this->perPage);
        }


        // Пагинация из объекта
        $productsArr = collect($productsArr);
        $currentPage = Paginator::resolveCurrentPage();
        $currentPageItems = $productsArr->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
        $products = new Paginator($currentPageItems, count($productsArr), $this->perPage);
        $products->setPath($request->url());
        $products->appends($request->all());

        // Если ajax, то отдадим вид для фильтров
        if ($request->ajax()) {
            return view("{$this->viewPathModule}.filter", compact('products'))->render();
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
        $breadcrumbs = $this->breadcrumbs
            ->values($this->table)
            ->dynamic($values->id, 'category')
            ->add([[route('catalog') => 'catalog']])
            ->get();


        $this->setMeta($values->title ?? null, $values->description ?? null);
        return view("{$this->viewPathModule}.{$this->c}_show", compact('values', 'products', 'breadcrumbs'));
    }
}
