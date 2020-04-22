<?php

namespace App\Modules\Admin\Controllers;

use App\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class BannedIpController extends AppController
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
        App::viewExists("{$this->view}.{$f}", __METHOD__);
        $perpage = config('admin.settings.pagination');
        //$values = DB::table($this->table)->paginate($perpage);

        // Массив гет ключей для поиска
        $queryArr = [
            'id',
            'ip',
            'banned',
        ];
        $col = request()->query('col');
        $cell = request()->query('cell');


        // Если есть строка поиска
        if ($col && $cell) {
            $values = $this->model::where($col, 'LIKE', "%{$cell}%")->paginate($perpage);

        // Иначе выборка всех элементов из БД
        } else {
            $values = $this->model::paginate($perpage);
        }

        $this->setMeta(__('a.' . Str::ucfirst($this->table)));
        return view("{$this->view}.$f", compact('values', 'queryArr', 'col', 'cell'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create()
    {
        //
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function store(Request $request)
    {
        //
    }*/

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ((int)$id) {
            $f = __FUNCTION__;
            App::viewExists("{$this->view}.{$f}", __METHOD__);

            $values = DB::table($this->table)->find((int)$id);

            $this->setMeta(__("a.$f"));
            return view("{$this->view}.{$f}", compact('values'));
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
        session()->put('error', __('s.something_went_wrong'));
        return redirect()->route("admin.{$this->route}.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function edit($id)
    {
        //
    }*/

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function update(Request $request, $id)
    {
        //
    }*/

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ((int)$id) {
            if (DB::table($this->table)->where('id', $id)->update(['count' => '0', 'banned' => '0'])) {

                // Сообщение об успехе
                session()->put('success', __('s.cleared_successfully', ['id' => $id]));
                return redirect()->route("admin.{$this->route}.index");
            }
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
        session()->put('error', __('s.something_went_wrong'));
        return redirect()->route("admin.{$this->route}.index");
    }
}
