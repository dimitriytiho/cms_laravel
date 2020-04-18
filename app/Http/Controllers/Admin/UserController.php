<?php

namespace App\Http\Controllers\Admin;

use App\App;
use App\Helpers\Admin\Img;
use App\Helpers\Admin\Slug;
use App\User;
use App\UserLastData;
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
        $model = $this->model = '\App\\' . $this->class;
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
        App::viewExists("admin.{$this->view}.$f", __METHOD__);
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

        $this->setMeta(__('a.' . Str::ucfirst($this->table)));
        return view("admin.{$this->view}.$f", compact('values', 'queryArr', 'col', 'cell'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $f = __FUNCTION__;
        App::viewExists("admin.{$this->view}.{$this->template}", __METHOD__);

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

        $this->setMeta(__('a.' . Str::ucfirst($f)));
        return view("admin.{$this->view}.{$this->template}", compact('roles', 'statuses'));
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
                'tel' => 'required|string|max:190',
                'password' => 'required|string|min:6|same:password_confirmation',
                'role_id' => 'required|integer',
            ];
            $this->validate($request, $rules);
            $data = $request->all();

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
            App::viewExists("admin.{$this->view}.{$this->template}", __METHOD__);

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


            $this->setMeta(__("a.$f"));
            return view("admin.{$this->view}.{$this->template}", compact('values', 'roles', 'statuses', 'imgRequestName', 'imgUploadID'));
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
                'name' => 'required|string|max:190',
                'email' => "required|string|email|unique:users,email,{$id}|max:190",
                'tel' => 'required|string|max:190',
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

            // Если данные изменины
            $lastData = $this->model::with('role')->find((int)$id)->toArray();
            if (isset($lastData['role'])) unset($lastData['role']);
            $lastDataNew = [];
            $current = $values->toArray();

            if (array_diff($current, $lastData)) {

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
                session()->put('error', __('s.data_was_not_changed'));
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
                $img = $values->img ?? null;

                if ($values->delete()) {

                    // Удалим картинку с сервера, кроме картинки по-умолчанию
                    Img::deleteImg($img, config("admin.img{$this->class}Default"));

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


    // Разлогинить пользователя
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        return redirect()->route('index');
    }
}
