<?php

namespace App\Modules\Auth\Controllers;

use App\App;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class RegisterController extends AppController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    //use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('guest');

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->class);
        App::set('c', $c);
        View::share(compact('class', 'c', 'view'));
    }


    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    protected function showRegistrationForm(Request $request)
    {
        return view("{$this->viewPathModule}.{$this->view}");
    }


    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function register(Request $request)
    {
        if ($request->isMethod('post')) {

            // Валидация
            $rules = [
                'name' => ['required', 'string', 'max:190'],
                'email' => ['required', 'string', 'email', 'max:190', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'accept' => ['accepted'],
            ];
            $this->validate($request, $rules);

            // Данные запроса
            $request->offsetUnset('password_confirmation');
            $request->merge([
                'password' => Hash::make($request->password),
                'accept' => $request->accept ? '1' : '0',
                'ip' => $request->ip(),
            ]);

            // Сохранить данные в БД
            $user = new User();
            $user->fill($request->all());
            if ($user->save()) {

                // Авторизируем нового пользователя
                $this->guard()->login($user);

                return redirect()->route('login');
            }
        }
        App::getError('No post request', __METHOD__);
    }
}
