<?php

namespace App\Modules\Auth\Controllers;

use App\App;
use App\Modules\Admin\Models\BannedIp;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends AppController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;
    use ThrottlesLogins;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->class);
        App::set('c', $c);
        View::share(compact('class', 'c', 'view'));
    }


    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {

            $rules = [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ];
            $this->validate($request, $rules);

            // Laravel блокирует неправильные попытки входа
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);


                // Через n кол-во блокировой блокируется IP c помощью таблицы banned_ip
                $tableBanned = 'banned_ip';
                $bannedIpCount = (int)(App::get('settings')['banned_ip_count'] ?? null);
                $ip = $request->ip() ?? null;

                if ($bannedIpCount && $ip) {
                    $issetIP = $values = DB::table($tableBanned)->where('ip', $ip)->get();

                    // Если существует IP в таблице banned_ip
                    if (isset($issetIP[0])) {
                        $countLast = (int)$issetIP[0]->count;
                        $ifCountBig = $countLast > $bannedIpCount;

                        // Если число попыток больше n в настройках banned_ip_count
                        if ($ifCountBig) {

                            // Обновить запись с этим ip, изменив на banned = 1
                            DB::table($tableBanned)->where('ip', $ip)->update(['banned' => '1']);

                        } else {

                            // Обновить запись с этим ip, прибавив 1 к полю count
                            DB::table($tableBanned)->where('ip', $ip)->increment('count', 1);
                        }

                    // Если нет IP в таблице banned_ip
                    } else {

                        $data['ip'] = $ip;

                        $bannedIP = new BannedIp();
                        $bannedIP->fill($data);

                        if (!$bannedIP->save()) {

                            // Сообщение что-то пошло не так
                            $message = 'Error Banned IP save and in ' . __METHOD__;
                            Log::warning($message);
                        }
                    }
                }

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }
        App::getError('No post request', __METHOD__);
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

    public function username()
    {
        return 'email';
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
        $email = $request->email;
        $ip = $request->ip();

        // Записать ip пользователя в БД
        User::saveIpStatic($email, $ip);

        // Если пользователь админ или редактор запишем в логи об авторизации
        if (isset($user->role_id) && in_array($user->role_id, User::roleIdAdmin())) {
            Log::info('Authorization of user with access Admin. ' . App::dataUser());
        }

        return redirect()->route('home');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        return view("{$this->viewPathModule}.{$this->view}");
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect()->route('login');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }
}
