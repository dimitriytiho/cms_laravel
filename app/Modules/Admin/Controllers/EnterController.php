<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendMail;
use App\Models\Main;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Modules\Admin\Controllers\AppController as AppController;
use App\Helpers\User as userHelpers;

class EnterController extends AppController
{
    use ThrottlesLogins;

    protected $module = 'Admin'; // Название модуля
    protected $m;
    protected $namespace;
    protected $modulePath;
    protected $class;
    protected $c;
    protected $route;
    protected $viewPath;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $module = $this->module;
        $namespace = config('modules.namespace');
        $modulesPath = config('modules.path');
        if (!$module || !$namespace || !$modulesPath) {
            Main::getError('Not data in Module', __METHOD__);
        }

        $this->namespace = "{$namespace}\\{$module}";
        $this->modulePath = "{$modulesPath}/{$module}";
        $m = $this->m = Str::lower($module);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);

        $route = $this->route = $request->segment(1);
        $view = $this->view  = Str::snake($this->class);

        // Определяем папку с видами, как корневую, чтобы виды были доступны во всех вложенных модулях
        //$viewPath = $this->viewPath  = 'views';
        //view()->getFinder()->setPaths("{$this->modulePath}/{$viewPath}");

        view()->share(compact('module', 'm', 'class', 'c', 'route', 'view'));
    }


    public function index(Request $request)
    {
        // Если пользователь аутентифицирован как админ или редактор, то случиться редирект
        if (Auth::check() && Auth::user()->Admin()) {
            return redirect()->route('admin.main');
        } /*elseif (Auth::check()) {
            return redirect()->route('index');
        }*/

        // Сообщение об открытой странице входа
        Main::getError('Open the Admin login page', __METHOD__, false, 'warning');

        Main::viewExists("{$this->viewPath}.{$this->view}.index", __METHOD__);
        $this->setMeta(__("{$this->lang}::s.login"));
        return view("{$this->viewPath}.{$this->view}.index");
    }

    public function enterPost(Request $request)
    {
        if ($request->isMethod('post')) {

            // Сообщение о запросе
            Log::warning('Request Enter login. ' . Main::dataUser());

            $rules = [
                'email' => 'required|string|email',
                'password' => 'required|string',
                //'g-recaptcha-response' => 'required|recaptcha',
            ];
            $request->validate($rules);

            // Laravel блокирует неправильные попытки входа
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);


            /*$rules = [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ];
            $request->validate($rules);

            $this->incrementLoginAttempts($request);

            $credentials = $request->only('email', 'password');
            $remember = $request->remember ?? null;

            if (Auth::attempt($credentials, $remember)) {
                return redirect()->route('admin.main');
            }*/
        }
        Main::getError('No post request', __METHOD__);
        /*$credentials = $request->only('email', 'password');
        $remember = $request->remember ?? null;

        if (Auth::attempt($credentials, $remember)) { // Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1], $remember или так Auth::guard('admin')->attempt($credentials, $remember)
            return redirect()->route('admin.main');
        }*/
        //return redirect()->route('enter');
    }


    public function username()
    {
        return 'email';
    }


    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }


    protected function guard()
    {
        return Auth::guard();
    }


    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }


    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }


    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }


    protected function authenticated(Request $request, $user)
    {
        // Действия после успешной авторизации

        // Записать ip пользователя в БД
        $user->saveIp();

        // Сохранить сообщение об совершённом входе в админку
        Log::info('Authorization of user with access Admin. ' . Main::dataUser());

        return redirect()->route('admin.main');
    }


    protected function redirectPath()
    {
        return route('index');
    }


    /*protected function hasTooManyLoginAttempts(Request $request)
    {
        $attempts = 1;
        $lockoutMinites = 2;
        return $this->limiter()->tooManyAttempts($this->throttleKey($request), $attempts, $lockoutMinites);
    }*/


    /*public function index(Request $request)
    {
        //$request->session()->forget('user');
        //dump(session()->all()); // 123
        $auth_view = null;

        if ($request->post()) {
            if (!$request->session()->exists('user.email')) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                ]);

                // Сработает когда сделано более 5 неудачных попыток
                if ($this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return $this->sendLockoutResponse($request);
                }
                // Увеличение попыток входа
                $this->incrementLoginAttempts($request);

                // Если есть ошибки валидации, то они выведутся
                if ($validator->fails()) {
                    return redirect()->route('enter')->withErrors($validator)->withInput();
                }

                // Проверка Admin
                $user = new User();
                $user = $user->getUser($request->email);
                if (!$user || !$user->Admin()) {
                    // Сработает когда сделано более 5 неудачных попыток
                    if ($this->hasTooManyLoginAttempts($request)) {
                        $this->fireLockoutEvent($request);

                        return $this->sendLockoutResponse($request);
                    }
                    // Увеличение попыток входа
                    $this->incrementLoginAttempts($request);

                    session()->flash('error', __("{$this->lang}::auth.email_failed"));
                    $request->flashOnly('email');
                    return redirect()->route('enter');
                }

                // Создание кода для верификации
                $code = rand(0, 99999);
                //$code = 123;
                $request->session()->put('user.email', $request->email);
                $request->session()->put('user.code', $code);
                $request->session()->put('user.date', time());

                // Отправка на email кода для верификации
                try {
                    Mail::to($request->email)->send(new SendMail(__("{$this->lang}::a.Code"), $code));
                } catch (\Exception $e) {
                    Log::error("Error sending mail. Email: $request->email. " . Main::dataUser() . "Error: $e. In " . __METHOD__);
                }
                return redirect()->route('enter');

            // 2.Если есть в сессии код верификации
            } elseif ($request->session()->exists('user.code')) {
                if (!empty($request->confirm) && $request->confirm == $request->session()->get('user.code')) {
                    $request->session()->put('user.code_verification', '1');
                    $request->session()->forget('user.code');

                // Если неверный код верификации
                } else {
                    // Сработает когда сделано более 5 неудачных попыток
                    if ($this->hasTooManyLoginAttempts($request)) {
                        $this->fireLockoutEvent($request);

                        return $this->sendLockoutResponse($request);
                    }
                    // Увеличение попыток входа
                    $this->incrementLoginAttempts($request);

                    $request->session()->flash('error', __("{$this->lang}::auth.code_incorrect"));
                    $request->flashOnly('confirm');
                }
                return redirect()->route('enter');

            // 3. Если пройдена верификация
            } elseif ($request->session()->exists('user.code_verification')) {
                // Дописывается Email в $request
                if ($request->session()->exists('user.email')) {
                    $request->request->add(['email' => $request->session()->get('user.email')]);
                } else {
                    $request->session()->forget('user');
                }

                // Стандартная аутентификация laravel
                $this->validateLogin($request);
                if ($this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return $this->sendLockoutResponse($request);
                }

                $credentials = $this->credentials($request);
                if ($this->guard()->attempt($credentials, $request->has('remember'))) {

                    $this->afterLogin($request);

                    return $this->sendLoginResponse($request);
                }
                $this->incrementLoginAttempts($request);

                return $this->sendFailedLoginResponse($request);
            }
        }

        // Если пользователь аутентифицирован, то случиться редирект
        if (Auth::check() && Auth::user()->Admin()) {
            return redirect()->route('admin.main');
        } elseif (Auth::check()) {
            return redirect()->route('main');
        }

        // Проверка времени авторизации пользователя, если больше 10 минут, то удалиться сессия user
        if (session()->exists('user.date') && session()->get('user.date') + 600 < time()) {
            session()->forget('user');
        }

        if ($request->session()->exists('user.code')) {
            $auth_view = 'confirm';
        } elseif ($request->session()->exists('user.code_verification')) {
            $auth_view = 'password';
        }

        $form_name = 'enter_email';
        if ($auth_view == 'confirm') {
            $form_name = 'enter_code';
        } elseif ($auth_view == 'password') {
            $form_name = 'enter_pass';
        }

        // Сообщение об открытой странице входа
        Log::warning('Open the Admin login page. ' . Main::dataUser());
        Main::viewExists('admin.enter', __METHOD__);
        $this->setMeta(__("{$this->lang}::s.login"));
        return view('admin.enter', compact('auth_view', 'form_name'));
    }*/


    // ДЕЙСТВИЯ ПОСЛЕ УСПЕШНОЙ АВТОРИЗАЦИИ
    /*private function afterLogin($request)
    {
        $email = $request->email;
        $ip = $request->ip();

        // Записать ip пользователя в БД
        User::saveIpStatic($email, $ip);

        // Сохранить сообщение об совершённом входе в админку
        Log::info("Login Admin completed to the dashboard. " . Main::dataUser());
    }*/
}
