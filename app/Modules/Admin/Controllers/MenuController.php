<?php

namespace App\Modules\Admin\Controllers;

use App\App;
use App\Modules\Admin\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MenuController extends AppController
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
    public function index(Request $request)
    {
        // Записывается в куку текущее меню
        if ($request->query('value')) {
            return redirect()->route("admin.{$this->route}.index")->withCookie('current_menu_id', $request->query('value'), config('admin.cookie'));
        }

        $current_menu_id = $request->cookie('current_menu_id');
        if (!$current_menu_id) {
            $current_menu = DB::table('menu_name')->first();
            $current_menu_id = $current_menu ? $current_menu->id : null;
        }

        $f = __FUNCTION__;
        App::viewExists("{$this->view}.$f", __METHOD__);

        $menu = null;
        $values = null;

        // Если в таблице menu_name нет элементов, то ничего нельзя добавить в таблицу menu
        $menuNameCount = DB::table('menu_name')->count();
        $menuNameCount = $menuNameCount >= 1;

        if ($menuNameCount) {
            $menu = DB::table('menu_name')->select('id', 'title')->get();
            $perpage = config('admin.settings.pagination');
            $values = DB::table($this->table)->where('menu_name_id', $current_menu_id)->paginate($perpage);
        }

        $this->setMeta(__('a.' . Str::ucfirst($this->table)));
        return view("{$this->view}.$f", compact('menu', 'values', 'current_menu_id', 'menuNameCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $f = __FUNCTION__;
        App::viewExists("{$this->view}.{$this->template}", __METHOD__);

        $current_menu_id = $request->cookie('current_menu_id');
        if (!$current_menu_id) {
            $current_menu = DB::table('menu_name')->first();
            $current_menu_id = $current_menu ? $current_menu->id : null;
        }
        $menuName = DB::table('menu_name')->find($current_menu_id);

        // Если в таблице menu_name нет элементов, то ничего нельзя добавить в таблицу menu и поэтому не показываем в виде форму добавления
        $values = DB::table('menu_name')->count();

        $this->setMeta(__('a.' . Str::ucfirst($f)));
        return view("{$this->view}.{$this->template}", compact('current_menu_id', 'values', 'menuName'));
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
                'slug' => "required|string|max:190",
            ];
            $this->validate($request, $rules);
            $data = $request->all();
            //$data['slug'] = Slug::checkRecursion($this->table, $data['slug']);

            $values = new Menu();
            $values->fill($data);

            if ($values->save()) {

                // Удалить все кэши
                cache()->flush();

                // Сообщение об успехе
                session()->put('success', __('s.created_successfully', ['id' => $values->id]));
                return redirect()->route("admin.{$this->route}.edit", $values->id);
            }
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
        session()->put('error', __('s.something_went_wrong'));
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
            App::viewExists("{$this->view}.{$this->template}", __METHOD__);

            $current_menu_id = null;
            $values = null;
            $menuName = null;

            // Если в таблице menu_name нет элементов, то ничего нельзя добавить в таблицу menu
            $menuNameCount = DB::table('menu_name')->count();
            $menuNameCount = $menuNameCount >= 1;

            if ($menuNameCount) {
                $current_menu_id = \Illuminate\Support\Facades\Request::cookie('current_menu_id') ?: 1;
                $menuName = DB::table('menu_name')->find($current_menu_id);
                $values = DB::table($this->table)->find((int)$id);
            }

            // Записать в реестр parent_id
            if (!empty($values->parent_id)) {
                App::$registry->set('parent_id', $values->parent_id);
            }

            // Потомки в массиве
            $getIdParents = \App\Modules\Admin\Helpers\App::getIdParents($values->id ?? null, $this->table);

            $this->setMeta(__("a.$f"));
            return view("{$this->view}.{$this->template}", compact('values', 'getIdParents', 'current_menu_id', 'menuName'));
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
        session()->put('error', __('s.something_went_wrong'));
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
                'slug' => "required|string|max:190",
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            $values = $this->model::find((int)$id);

            // Уникальный slug
            /*$data['menu_name_id'] = $data['menu_name_id'] ?? 1;
            $data['slug'] = Slug::checkRecursion($this->table, $data['slug'], null, $values->id, 'menu_name_id', $data['menu_name_id']);*/

            // Если нет сортировки, то по-умолчанию 500
            $data['sort'] = empty($data['sort']) ? 500 : $data['sort'];
            $values->fill($data);

            // Если данные не изменины
            $lastData = $this->model::find((int)$id)->toArray();
            $current = $values->toArray();
            if (!array_diff($current, $lastData)) {

                // Сообщение об ошибке
                session()->put('error', __('s.data_was_not_changed'));
                return redirect()->route("admin.{$this->route}.edit", $values->id);
            }

            if ($values->save()) {

                // Удалить все кэши
                cache()->flush();

                // Сообщение об успехе
                session()->put('success', __('s.saved_successfully', ['id' => $values->id]));
                return redirect()->route("admin.{$this->route}.edit", $values->id);
            }
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
        session()->put('error', __('s.something_went_wrong'));
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

                // Если есть потомки, то ошибка
                $getIdParents = \App\Modules\Admin\Helpers\App::getIdParents((int)$id, $this->table);
                if ($getIdParents) {
                    session()->put('error', __('s.remove_not_possible') . ', ' . __('s.there_are_nested') . ' #');
                    return redirect()->route("admin.{$this->route}.index");
                }

                if ($values->delete()) {

                    // Удалить все кэши
                    cache()->flush();

                    // Сообщение об успехе
                    session()->put('success', __('s.removed_successfully', ['id' => $values->id]));
                    return redirect()->route("admin.{$this->route}.index");
                }
            }
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
        session()->put('error', __('s.something_went_wrong'));
        return redirect()->route("admin.{$this->route}.index");
    }
}
