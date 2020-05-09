<?php

namespace App\Modules\Admin\Controllers;

use App\App;
use App\Modules\Admin\Helpers\App as appHelpers;
use App\Modules\admin\Helpers\Img;
use App\Modules\admin\Helpers\Slug;
use App\Modules\Admin\Models\User;
use App\Modules\Admin\Models\UserLastData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class UserController extends AppController
{
    private $guardedLast = ['note', 'accept', 'email_verified_at', 'remember_token', 'created_at', 'updated_at'];


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
        App::viewExists("{$this->view}.$f", __METHOD__);
        $perpage = config('admin.settings.pagination');
        //$values = $this->model::with('role')->paginate($perpage);

        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'id',
            'name',
            'email',
            'tel',
            'role_id',
            'ip',
        ];
        $col = request()->query('col');
        $cell = request()->query('cell');

        // Автоматически определим по какому ключу искать
        /*$requestQuery = request()->query() ?: [];
        $query = $requestQuery ? key($requestQuery) : null;

        // Определим строку для поиска
        $search = in_array($query, $queryArr) && isset($requestQuery[$query]) ? $requestQuery[$query] : null;*/

        // Если есть строка поиска
        if ($col && $cell) {
            $values = $this->model::where($col, 'LIKE', "%{$cell}%")->paginate($perpage);

            // Иначе выборка всех элементов из БД
        } else {
            $values = $this->model::paginate($perpage);
        }

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($this->table)));
        return view("{$this->view}.$f", compact('values', 'queryArr', 'col', 'cell'));
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

        // Статусы пользователей
        $statuses = config('admin.user_statuses');

        // Роли преобразуются в массив
        $roles_obj = DB::table('roles')->select('id', 'name')->get();
        $roles = [];
        if (!empty($roles_obj)) {
            foreach ($roles_obj as $v) {
                $roles[$v->id] = $v->name;
            }
        }

        // Если не Админ, то запишим id роли Админ
        $roleIdAdmin = !auth()->user()->isAdmin() ? auth()->user()->getRoleIdAdmin() : null;

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->view}.{$this->template}", compact('roles', 'statuses', 'roleIdAdmin'));
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
                'name' => 'required|string|max:190',
                'email' => "required|string|email|unique:users,email|max:190",
                //'tel' => 'required|string|max:190',
                'password' => 'required|string|min:6|same:password_confirmation',
                'role_id' => 'required|integer',
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            // Если не Админ выбирает роль Админ, то ошибка
            if (!auth()->user()->isAdmin() && $data['role_id'] == auth()->user()->getRoleIdAdmin()) {

                // Сообщение об ошибке
                session()->put('error', __("{$this->lang}::s.admin_choose_admin"));
                return redirect()->back();
            }

            // Если нет картинки
            if (empty($data['img'])) {
                $data['img'] = config("admin.img{$this->class}Default");
            }

            // Поле подтверждение пароля удаляется
            unset($data['password_confirmation']);

            // Если есть пароль, то он хэшируется
            if ($data['password']) {
                $data['password'] = Hash::make($data['password']);
            }

            $values = new User();
            $values->fill($data);

            if ($values->save()) {

                // Сообщение об успехе
                session()->put('success', __("{$this->lang}::s.created_successfully", ['id' => $values->id]));
                return redirect()->route("admin.{$this->route}.edit", $values->id);
            }
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
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
            App::viewExists("{$this->view}.{$this->template}", __METHOD__);

            $values = $this->model::with('role')->find((int)$id);

            // Статусы пользователей
            $statuses = config('admin.user_statuses');

            // Роли преобразуются в массив
            $roles_obj = DB::table('roles')->select('id', 'name')->get();
            $roles = [];
            if (!empty($roles_obj)) {
                foreach ($roles_obj as $v) {
                    $roles[$v->id] = $v->name;
                }
            }


            // DROPZONE DATA
            // Передаём начальную часть названия для передаваемой картинки Dropzone JS
            $imgRequestName = $this->imgRequestName = Slug::cyrillicToLatin($values->name, 32);

            // ID элемента, для которого картинка Dropzone JS
            $imgUploadID = $this->imgUploadID = $values->id;

            // Если не Админ, то запишим id роли Админ
            $roleIdAdmin = !auth()->user()->isAdmin() ? auth()->user()->getRoleIdAdmin() : null;

            $this->setMeta(__("{$this->lang}::a.{$f}"));
            return view("{$this->view}.{$this->template}", compact('values', 'roles', 'statuses', 'imgRequestName', 'imgUploadID', 'roleIdAdmin'));
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
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
                'name' => 'required|string|max:190',
                'email' => "required|string|email|unique:users,email,{$id}|max:190",
                //'tel' => 'required|string|max:190',
                'role_id' => 'required|integer',
                //'password' => 'same:password_confirmation',
            ];
            $this->validate($request, $rules);
            $data = $request->all();

            // Если нет картинки
            if (empty($data['img'])) {
                $data['img'] = config("admin.img{$this->class}Default");
            }

            // Поле подтверждение пароля удаляется
            unset($data['password_confirmation']);

            // Поле пароль удаляется, т.к. оно меняет через JS
            unset($data['password']);

            // Если есть пароль, то он хэшируется
            /*if ($data['password']) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }*/

            $values = $this->model::find((int)$id);
            $values->fill($data);

            // Если не Админ выбирает роль Админ, то ошибка
            if (!auth()->user()->isAdmin() && $data['role_id'] == auth()->user()->getRoleIdAdmin()) {

                // Сообщение об ошибке
                session()->put('error', __("{$this->lang}::s.admin_choose_admin"));
                return redirect()->back();
            }

            // Если данные изменины
            $lastData = $this->model::with('role')->find((int)$id)->toArray();
            if (isset($lastData['role'])) unset($lastData['role']);
            $lastDataNew = [];
            $current = $values->toArray();
            if (appHelpers::arrayDiff($lastData, $current)) {

                // В таблицу users_last_data запишутся предыдущие данные
                foreach ($lastData as $k => $v) {

                    // Исключаем не нужные поля
                    if (!in_array($k, $this->guardedLast)) {
                        $lastDataNew[$k] = $v;
                    }
                }
                $lastDataNew['user_id'] = $lastData['id'];

                // Сохраняем данные
                if ($lastDataNew) {
                    $last = new UserLastData();
                    $last->fill($lastDataNew);

                    if (!$last->save()) {

                        // Сообщение что-то пошло не так
                        $message = 'Error UserLastData save and in ' . __METHOD__;
                        Log::warning($message);
                    }
                }

            } else {

                // Сообщение об ошибке
                session()->put('error', __("{$this->lang}::s.data_was_not_changed"));
                return redirect()->route("admin.{$this->route}.edit", $values->id);
            }

            if ($values->save()) {

                // Если меняются данные текущего пользователя, то изменим их в объекте auth
                if ($values->id === auth()->user()->id) {
                    $auth = auth()->user()->toArray();
                    if ($auth) {
                        unset($auth['img']); // Удалим из массива картинку, т.к. она меняется сразу при смене картинки
                        foreach ($auth as $authKey => $authValue) {
                            if (isset($data[$authKey]) && $data[$authKey] != $authValue) {
                                auth()->user()->update([$authKey => $data[$authKey]]);
                            }
                        }
                    }
                }

                // Сообщение об успехе
                session()->put('success', __("{$this->lang}::s.saved_successfully", ['id' => $values->id]));
                return redirect()->route("admin.{$this->route}.edit", $values->id);
            }
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
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

                // Если включен shop
                if (config('add.shop')) {

                    // Проверим есть ли заказы
                    $orders = DB::table('orders')->where('user_id', (int)$id)->get()->toArray();
                    if ($orders) {
                        $ordersPart = '';
                        foreach ($orders as $order) {
                            $ordersPart .= "#{$order->id} ,";
                        }
                        $ordersPart = rtrim($ordersPart, ' ,');

                        session()->put('error', __("{$this->lang}::s.user_has") . Str::lower(__("{$this->lang}::a.Orders")) . " {$ordersPart}");
                        return redirect()->back();
                    }
                }

                if ($values->delete()) {

                    // Удалим картинку с сервера, кроме картинки по-умолчанию
                    Img::deleteImg($img, config("admin.img{$this->class}Default"));

                    // Сообщение об успехе
                    session()->put('success', __("{$this->lang}::s.removed_successfully", ['id' => $values->id]));
                    return redirect()->route("admin.{$this->route}.index");
                }
            }
        }

        // Сообщение об ошибке
        App::getError('Request', __METHOD__, null);
        session()->put('error', __("{$this->lang}::s.something_went_wrong"));
        return redirect()->route("admin.{$this->route}.index");
    }


    // Разлогинить пользователя
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        return redirect()->route('index');
    }
}
