<?php

namespace App\Modules\Admin\Controllers;

use App\Models\{Main, User};
use App\Modules\Admin\Helpers\Slug;
use App\Helpers\Upload;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MainController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $this->c = strtolower($this->class);
        $view = $this->view = Str::snake($this->class);
        View::share(compact('class','view'));
    }


    public function index()
    {
        $f = __FUNCTION__;
        Main::viewExists("{$this->viewPath}.{$this->view}.{$f}", __METHOD__);
        $count_forms = DB::table('forms')->count();
        $count_pages = DB::table('pages')->count();

        // Если включена авторизация на сайте, то покажем кол-во новых пользователей, а если нет, то покажем кол-во всех пользователей.
        if (config('add.auth')) {
            $idRolesPublic = User::roleIdAdmin(1);

            $count_users = DB::table('users')->whereIn('role_id',$idRolesPublic)->count();

        } else {
            $count_users = DB::table('users')->count();
        }


        $count_orders = DB::table('orders')->where('status', config('admin.order_statuses')[0])->count();
        $key = null;

        // Взязь из кэша
        //cache()->flush();
        if (cache()->has('key_to_enter')) {
            $key = cache()->get('key_to_enter');

        } else {

            // Запрос в БД
            $key = DB::table('uploads')->orderBy('id', 'desc')->first();
            if (isset($key)) {

                // Кэшируется запрос
                cache()->forever('key_to_enter', $key);
            }
        }

        $this->setMeta(__("{$this->lang}::a.Dashboard"));
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('count_forms', 'count_pages', 'count_users', 'key', 'count_orders'));
    }


    // Записывает в куку локаль
    public function locale($locale)
    {
        $locales = config('admin.locales');
        if (in_array($locale, $locales)) {

            try {
                $locale = Crypt::encryptString($locale);
            } catch (DecryptException $e) {
                Main::getError('Error Crypt::encryptString', __METHOD__, false);
            }

            return redirect()->back()->withCookie(config('add.name') . '_loc', $locale);
        }
        Main::getError("Invalid locale $locale", __METHOD__);
    }


    public function cyrillicToLatin(Request $request)
    {
        if ($request->ajax()) {
            return empty($request->title) ? '' : Slug::cyrillicToLatin($request->title);
        }
        Main::getError('Request No Ajax', __METHOD__);
    }


    public function toChangeKey(Request $request)
    {
        if ($request->ajax()) {
            $key = $request->key ?? null;
            if ($key) {
                Upload::getNewKey($key);
                return __("{$this->lang}::a.key_success");
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
    }


    public function userChangePassword(Request $request)
    {
        if ($request->ajax()) {
            $userId = $request->userId ?? null;
            $password = $request->password ?? null;

            if ($userId && $password) {
                $values = User::find((int)$userId);

                if ($values) {
                    $values->password = Hash::make($password);

                    if ($values->save()) {
                        return __("{$this->lang}::s.changed_successfully", ['id' => $userId]);
                    }
                }
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
    }
}
