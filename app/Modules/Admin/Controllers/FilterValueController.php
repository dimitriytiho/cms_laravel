<?php

namespace App\Modules\Admin\Controllers;

use App\Helpers\Add;
use App\Main;
use App\Modules\Admin\Helpers\App as appHelpers;
use App\Modules\Admin\Models\FilterValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class FilterValueController extends AppController
{
    private $parentTable = 'filter_groups';
    private $parentRoute = 'filter-group';
    private $belongsTable = 'filter_products';
    private $belongsRoute = 'product';


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $parentTable = $this->parentTable;
        $parentRoute = $this->parentRoute;
        $belongsTable = $this->belongsTable;
        $belongsRoute = $this->belongsRoute;
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $model = $this->model = '\App\\Modules\\Admin\\Models\\' . $this->class;
        $table = $this->table = with(new $model)->getTable(); // Получаем название таблицы
        $route = $this->route = $request->segment(2); // Получаем сегмент из url
        $view = $this->view = Str::snake($this->class); // Преобразуем в foo_bar
        View::share(compact('class','model', 'table', 'route', 'view', 'parentTable', 'parentRoute', 'belongsTable', 'belongsRoute'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Если через Get передаётся значение, то записывается в куку текущее меню
        $queryValue = $request->query('value');
        if ($queryValue) {
            return redirect()->back()->withCookie("{$this->view}_id", $request->query('value'), config('admin.cookie'));
        }

        $currentParentId = $request->cookie("{$this->view}_id");
        $countParent = DB::table($this->parentTable)->count();

        if (!$currentParentId && $countParent) {
            $currentParent = DB::table($this->parentTable)->first();
            $currentParentId = $currentParent->id;

            return redirect()->back()->withCookie("{$this->view}_id", $currentParentId, config('admin.cookie'));
        }

        $f = __FUNCTION__;
        Main::viewExists("{$this->view}.{$f}", __METHOD__);

        $parentValues = null;
        $values = null;

        // Если в родительской таблице нет элементов, то ничего нельзя добавить
        $parentCount = DB::table($this->parentTable)->count();

        if ($parentCount > 0) {
            $parentValues = DB::table($this->parentTable)->select('id', 'title')->get();
            $values = DB::table($this->table)->where('parent_id', $currentParentId)->paginate($this->perPage);
        }

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($this->view)));
        return view("{$this->view}.{$f}", compact('parentValues', 'values', 'currentParentId', 'parentCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->view}.{$this->template}", __METHOD__);

        $countParent = DB::table($this->parentTable)->count();
        $queryCookie = $request->cookie("{$this->view}_id");

        // Записывается в куку текущее меню
        if ($countParent && !$queryCookie) {
            return redirect()->back()->withCookie("{$this->view}_id", $request->query('value'), config('admin.cookie'));
        }

        $currentParentId = $request->cookie("{$this->view}_id");
        if (!$currentParentId) {
            $current_menu = DB::table($this->parentTable)->first();
            $currentParentId = $current_menu->count() > 0 ? $current_menu->id : null;
        }
        $parentValues = DB::table($this->parentTable)->find($currentParentId);

        // Если в родительской таблице нет элементов, то ничего нельзя добавить и поэтому не показываем в виде форму добавления
        $values = DB::table($this->parentTable)->count();

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->view}.{$this->template}", compact('currentParentId', 'values', 'parentValues'));
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
                'value' => "required|string|max:190",
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            $values = new FilterValue();
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

            $currentParentId = null;
            $values = null;
            $parentValues = null;

            // Если в родительской таблице нет элементов, то ничего нельзя добавить
            $parentCount = DB::table($this->parentTable)->count();

            if ($parentCount > 0) {
                $currentParentId = request()->cookie("{$this->view}_id") ?: 1;
                $parentValues = DB::table($this->parentTable)->find($currentParentId);
                $values = DB::table($this->table)->find((int)$id);
            }

            // Записать в реестр parent_id
            if (!empty($values->parent_id)) {
                Main::set('parent_id', $values->parent_id);
            }

            // Потомки в массиве
            $getIdParents = appHelpers::getIdParents($values->id ?? null, $this->belongsTable, 'value_id');

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->view}.{$this->template}", compact('values', 'getIdParents', 'currentParentId', 'parentValues'));
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
                'value' => "required|string|max:190",
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            // Если нет сортировки, то по-умолчанию 500
            $data['sort'] = empty($data['sort']) ? 500 : $data['sort'];

            $values = $this->model::find((int)$id);
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

                // Если есть родители, то ошибка
                $getIdParents = appHelpers::getIdParents((int)$id, $this->belongsTable, 'value_id');
                if ($getIdParents) {
                    session()->put('error', __("{$this->lang}::s.remove_not_possible") . ', ' . __("{$this->lang}::s.there_are_nested") . ' #');
                    return redirect()->route("admin.{$this->route}.edit", $id);
                }

                if ($values->delete()) {

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
