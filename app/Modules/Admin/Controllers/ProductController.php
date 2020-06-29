<?php

namespace App\Modules\Admin\Controllers;

use App\Main;
use App\Modules\Admin\Helpers\App as appHelpers;
use App\Modules\Admin\Helpers\DbSort;
use App\Modules\Admin\Helpers\Img;
use App\Modules\Admin\Helpers\Slug;
use App\Modules\Admin\Models\FilterGroup;
use App\Modules\Admin\Models\FilterProduct;
use App\Modules\Admin\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ProductController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $model = $this->model = '\App\\Modules\\Admin\\Models\\' . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);
        View::share(compact('class','model', 'table', 'route', 'view'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->view}.{$f}", __METHOD__);

        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'id',
            'title',
            'slug',
            'status',
            'sort',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Метод для поиска и сортировки запроса БД
        $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage);

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($this->table)));
        return view("{$this->view}.{$f}", compact('values', 'queryArr', 'col', 'cell'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->view}.{$this->template}", __METHOD__);

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->view}.{$this->template}");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                'title' => 'required|string|max:190',
                'slug' => "required|string|unique:{$this->table}|max:190",
                'price' => 'required',
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            // Уникальный slug
            //$data['slug'] = Slug::checkRecursion($this->table, $data['slug']);

            // Приводим цену к float
            $data['old_price'] = is_float($data['old_price']) ? $data['old_price'] : floatval($data['old_price']);
            $data['price'] = is_float($data['price']) ? $data['price'] : floatval($data['price']);

            // Если нет картинки, то по-умолчанию
            if (empty($data['img'])) {
                $data['img'] = config("admin.img{$this->class}Default");
            }

            // Если нет body, то ''
            if (empty($data['body'])) {
                $data['body'] = '';
            }

            $values = new Product();
            $values->fill($data);

            if ($values->save()) {

                // Удалить все кэши
                cache()->flush();

                // Сообщение об успехе
                session()->put('success', __("{$this->lang}::s.created_successfully", ['id' => $values->id]));
                return redirect()->route("admin.{$this->route}.edit", $values->id);
            }
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        session()->put('error', __("{$this->lang}::s.something_went_wrong"));
        return redirect()->route("admin.{$this->route}.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ((int)$id) {
            $f = __FUNCTION__;
            Main::viewExists("{$this->view}.{$this->template}", __METHOD__);

            $values = $this->model::with('category')->find((int)$id);
            if (!$values) {

                // Сообщение об ошибке
                Main::getError('Request', __METHOD__, null);
                session()->put('error', __("{$this->lang}::s.something_went_wrong"));
                return redirect()->route("admin.{$this->route}.index");
            }

            $filterGroups = FilterGroup::all()->keyBy('id');
            $filters = DB::table('filter_values')->get();
            $filtersActive = $this->model::with('filter_values')->find((int)$id);

            $gallery = DB::table("{$this->c}_gallery")->where("{$this->c}_id", $values->id)->get();

            // Собираем в массиве категориии, в которых этот товар и передаём их в меню select
            $disabledIDs = [];
            //$disabledID = appHelpers::getIdParents($values->id ?? null, $this->table);
            if ($values->category) {
                foreach ($values->category as $cat) {
                    $disabledIDs[] = $cat->id;
                }
            }
            Main::set('disabledIDs', $disabledIDs);


            // DROPZONE DATA
            // Передаём начальную часть названия для передаваемой картинки Dropzone JS
            $imgRequestName = $this->imgRequestName = Slug::cyrillicToLatin($values->title, 32);

            // ID элемента, для которого картинка Dropzone JS
            $imgUploadID = $this->imgUploadID = $values->id;

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->view}.{$this->template}", compact('values', 'filterGroups', 'filters', 'filtersActive', 'imgRequestName', 'imgUploadID', 'gallery'));
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        session()->put('error', __("{$this->lang}::s.something_went_wrong"));
        return redirect()->route("admin.{$this->route}.index");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ((int)$id && $request->isMethod('put')) {
            $rules = [
                'title' => 'required|string|max:190',
                'slug' => "required|string|unique:{$this->table},slug,{$id}|max:190",
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            $values = $this->model::find((int)$id);
            if ($values) {

                // Уникальный slug
                //$data['slug'] = Slug::checkRecursion($this->table, $data['slug'], null, $values->id);

                // Приводим цену к float
                $data['old_price'] = is_float($data['old_price']) ? $data['old_price'] : floatval($data['old_price']);
                $data['price'] = is_float($data['price']) ? $data['price'] : floatval($data['price']);

                // Если нет картинки, то по-умолчанию
                if (empty($data['img'])) {
                    $data['img'] = config("admin.img{$this->class}Default");
                }

                // Если нет сортировки, то по-умолчанию 500
                $data['sort'] = empty($data['sort']) ? 500 : $data['sort'];

                // Удаляем category_id, т.к. он нужен, чтобы на JS сохранить категории для товара
                if (isset($data['category_id'])) unset($data['category_id']);

                // Удаляем filter_value, т.к. он нужен, чтобы на JS сохранить filter для товара
                if (isset($data['filter_value'])) unset($data['filter_value']);

                $values->fill($data);

                // Если данные не изменины
                $lastData = $this->model::find((int)$id)->toArray();
                $current = $values->toArray();
                if (!appHelpers::arrayDiff($lastData, $current)) {

                    // Сообщение об ошибке
                    session()->put('error', __("{$this->lang}::s.data_was_not_changed"));
                    return redirect()->route("admin.{$this->route}.edit", $values->id);
                }

                if ($values->save()) {

                    // Удалить все кэши
                    cache()->flush();

                    // Сообщение об успехе
                    session()->put('success', __("{$this->lang}::s.saved_successfully", ['id' => $values->id]));
                    return redirect()->route("admin.{$this->route}.edit", $values->id);
                }
            }
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        session()->put('error', __("{$this->lang}::s.something_went_wrong"));
        return redirect()->route("admin.{$this->route}.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ((int)$id) {

            $values = $this->model::find((int)$id);

            if ($values) {
                $img = $values->img ?? null;

                // Проверим есть ли заказы
                $orders = DB::table('order_product')->where('product_id', (int)$id)->get()->toArray();
                if ($orders) {
                    $ordersPart = '';
                    foreach ($orders as $order) {
                        $ordersPart .= "#{$order->order_id} ,";
                    }
                    $ordersPart = rtrim($ordersPart, ' ,');

                    session()->put('error', __("{$this->lang}::s.product_is_present_in_order") . $ordersPart);
                    return redirect()->back();
                }


                // УДАЛЯЕМ ВСЕ СВЯЗКИ С ТОВАРОМ
                // С категориями и сам товар
                DB::table("category_{$this->view}")->where("{$this->view}_id", (int)$id)->delete();

                // Удалим сначало картинки галереи, а потом связку
                Img::deleteImgAll("{$this->view}_gallery", "{$this->view}_id", $id);

                // Товар и его картинки галереи
                DB::table("{$this->view}_gallery")->where("{$this->view}_id", (int)$id)->delete();

                if ($values->delete()) {

                    // Удалим картинку с сервера, кроме картинки по-умолчанию
                    Img::deleteImg($img, config("admin.img{$this->class}Default"));

                    // Удалить все кэши
                    cache()->flush();

                    // Сообщение об успехе
                    session()->put('success', __("{$this->lang}::s.removed_successfully", ['id' => $values->id]));
                    return redirect()->route("admin.{$this->route}.index");
                }
            }
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        session()->put('error', __("{$this->lang}::s.something_went_wrong"));
        return redirect()->route("admin.{$this->route}.index");
    }
}
