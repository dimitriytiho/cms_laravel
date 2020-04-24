<?php

namespace App\Modules\Admin\Controllers;

use App\App;
use App\Modules\Admin\Models\MenuName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MenuNameController extends AppController
{
    private $belongsToTable;


    public function __construct(Request $request)
    {
        $belongsToTable = $this->belongsToTable = 'menu';

        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $model = $this->model = '\App\\Modules\\Admin\\Models\\' . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);
        View::share(compact('class','model', 'table', 'route', 'view', 'belongsToTable'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $f = __FUNCTION__;
        App::viewExists("{$this->view}.$f", __METHOD__);

        $perpage = config('admin.settings.pagination');
        $values = DB::table($this->table)->paginate($perpage);

        $this->setMeta(__('a.' . Str::ucfirst($this->table)));
        return view("{$this->view}.$f", compact('values'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $f = __FUNCTION__;
        App::viewExists("{$this->view}.{$this->template}", __METHOD__);

        $this->setMeta(__('a.' . Str::ucfirst($f)));
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
                'title' => "required|string|unique:menu_name,title|max:190",
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            $values = new MenuName();
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

            $values = DB::table($this->table)->find((int)$id);

            // Потомки в массиве
            $getIdParents = \App\Modules\Admin\Helpers\App::getIdParents((int)$id, $this->belongsToTable, "{$this->table}_id");

            $this->setMeta(__("a.$f"));
            return view("{$this->view}.{$this->template}", compact('values', 'getIdParents'));
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
                'title' => "required|string|unique:menu_name,title,{$id}|max:190",
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            $values = $this->model::find((int)$id);
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
                $getIdParents = \App\Modules\Admin\Helpers\App::getIdParents((int)$id, $this->belongsToTable, "{$this->table}_id");
                if ($getIdParents) {
                    session()->put('error', __('s.remove_not_possible') . ', ' . __('s.there_are_nested') . ' #');
                    return redirect()->route("admin.{$this->route}.index");
                }

                if ($values->delete()) {

                    // Удалить все кэши
                    cache()->flush();

                    // Сообщение об успехе
                    session()->put('success', __('s.removed_successfully', ['id' => $values->id]));

                    // Если удаляется меню с id, который записан в куку, то перезапишем в куку id другого меню
                    $cookie = \Illuminate\Support\Facades\Request::query('value');
                    if ($cookie && $cookie == $id) {
                        $newCookie = DB::table($this->table)->first()->id;

                        return redirect()->route("admin.{$this->route}.index")->withCookie('current_menu_id', $newCookie, config('admin.cookie'));
                    }

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
