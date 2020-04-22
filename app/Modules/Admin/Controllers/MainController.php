<?php

namespace App\Modules\Admin\Controllers;

use App\App;
use App\Modules\Admin\Helpers\Slug;
use App\Helpers\Upload;
use App\User;
use Illuminate\Http\Request;
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
        $view = $this->view = Str::snake($this->class);
        View::share(compact('class','view'));
    }


    public function index()
    {
        $f = __FUNCTION__;
        App::viewExists("{$this->c}.$f", __METHOD__);
        $count_forms = DB::table('forms')->count();
        $count_pages = DB::table('pages')->count();
        $count_users = DB::table('users')->count();
        $count_orders = DB::table('orders')->where('status', config('admin.order_statuses')[0])->count();
        $key = null;

        // Взязь из кэша
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

        $this->setMeta(__("a.{$this->currentRoute['title']}"));
        return view("{$this->view}.{$f}", compact('count_forms', 'count_pages', 'count_users', 'key', 'count_orders'));
    }

    /*
     * Записывает в куку локаль.
     * Не будет работать если установлен домен с портом localhost:8888.
     */
    public function locale($locale)
    {
        $locales = config('admin.locales');
        if (in_array($locale, $locales)) {
            $locale = Crypt::encryptString($locale);
            setcookie('loc', $locale, time() + config('admin.cookie'), '/', config('add.domain'), config('add.protocol') === 'https', true);
            return redirect()->back();
            //return redirect()->back()->withCookie('locale', $locale, config('admin.cookie'));
        }
        App::getError("Invalid locale $locale", __METHOD__);
    }


    public function cyrillicToLatin(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            return $request->title ? Slug::cyrillicToLatin($request->title ?? null) : '';
        }
        App::getError('Request No Ajax', __METHOD__);
    }


    public function toChangeKey(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            $key = $request->key ?? null;
            if ($key) {
                Upload::getNewKey($key);
                return __('a.key_success');
            }
        }
        App::getError('Request No Ajax', __METHOD__);
    }


    public function userChangePassword(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            $userId = $request->userID ?? null;
            $password = $request->password ?? null;

            if ($userId && $password) {
                $values = User::find((int)$userId);

                if ($values) {
                    $values->password = Hash::make($password);

                    if ($values->save()) {
                        return __('s.changed_successfully', ['id' => $userId]);
                    }
                }
            }
        }
        App::getError('Request No Ajax', __METHOD__);
    }
}
