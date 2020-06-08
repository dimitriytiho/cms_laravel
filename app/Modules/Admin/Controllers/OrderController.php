<?php

namespace App\Modules\Admin\Controllers;

use App\Main;
use App\Modules\Admin\Helpers\App as appHelpers;
use App\Modules\Admin\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class OrderController extends AppController
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
        //$values = $this->model::orderBy('id', 'desc')->paginate($this->perPage); // Такой запрос, если используется связь к таблице пользователей


        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'id',
            'user_id',
            'status',
            'ip',
        ];
        $col = request()->query('col');
        $cell = request()->query('cell');

        // Если есть строка поиска
        if ($col && in_array($col, $queryArr) && $cell) {
            $values = $this->model::where($col, 'LIKE', "%{$cell}%")->orderBy('id', 'desc')->paginate($this->perPage);

        // Иначе выборка всех элементов из БД
        } else {
            $values = $this->model::orderBy('id', 'desc')->paginate($this->perPage);
        }

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($this->table)));
        return view("{$this->view}.{$f}", compact('values', 'queryArr', 'col', 'cell'));
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
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ((int)$id) {
            $f = __FUNCTION__;
            Main::viewExists("{$this->view}.{$f}", __METHOD__);

            // Статусы пользователей
            $statuses = config('admin.order_statuses');
            $values = $this->model::find((int)$id); // Такой запрос, если используется связь with('OrderProduct')->


            // Получаем из таблицы OrderProduct вместе с таблицей order
            //$va = OrderProduct::with('order')->where('order_id', (int)$id)->get();

            $orderProducts = OrderProduct::with('product')->where('order_id', (int)$id)->get();

            /*foreach ($orderProducts as $g) {
                dump($g->sum);
                dump($g->product->title);
            }*/
            //dump($orderProducts); //->product->id

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->view}.{$f}", compact('values', 'statuses', 'orderProducts'));
        }

        // Сообщение об ошибке
        Main::getError('Request', __METHOD__, null);
        session()->put('error', __("{$this->lang}::s.something_went_wrong"));
        return redirect()->route("admin.{$this->route}.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    /*public function edit(Order $order)
    {
        //
    }*/

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ((int)$id && $request->isMethod('put')) {

            $data = $request->all();
            $values = $this->model::find((int)$id);
            $values->fill($data);

            // Если данные не изменины
            $lastData = $this->model::find((int)$id)->toArray();
            $current = $values->toArray();

            if (!appHelpers::arrayDiff($lastData, $current)) {

                // Сообщение об ошибке
                session()->put('error', __("{$this->lang}::s.data_was_not_changed"));
                return redirect()->route("admin.{$this->route}.show", $values->id);
            }

            if ($values->save()) {

                // Сообщение об успехе
                session()->put('success', __("{$this->lang}::s.saved_successfully", ['id' => $values->id]));
                return redirect()->route("admin.{$this->route}.show", $values->id);
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
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ((int)$id) {

            $values = $this->model::find((int)$id);

            if ($values) {

                $orderProduct = DB::table('order_product')->where('order_id', (int)$id)->delete();

                if ($values->delete() && $orderProduct) {

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
