<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Main;
use App\Helpers\Services\Registry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use ReCaptcha\ReCaptcha;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ЗДЕСЬ ПИСАТЬ КОД, КОТОРЫЙ ЗАПУСКАЕТСЯ ПЕРЕД ЗАГРУЗКОЙ ПРИЛОЖЕНИЙ
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);


        // ЗДЕСЬ ПИСАТЬ КОД, КОТОРЫЙ ЗАПУСКАЕТСЯ ПОСЛЕ ЗАГРУЗКИ ВСЕХ СЕРВИС-ПРОВАЙДЕРОВ

        // Добавляем Google ReCaptcha в валидатор
        Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $recaptcha = new ReCaptcha(env('RECAPTCHA_SECRET_KEY'));
            $resp = $recaptcha->verify($value, request()->ip());

            return $resp->isSuccess();
        });


        // Добавляем папку для переводов, т.е. namespace для переводов
        $modulesPath = config('modules.path');
        $modulesNamespace = config('modules.namespace');
        $modulesLang = config('modules.lang');
        $this->loadTranslationsFrom("{$modulesPath}/{$modulesLang}", "{$modulesNamespace}\\{$modulesLang}");


        // Паттерн реестр
        Main::$registry = Registry::instance();

        // Если индексирование сайта выключено
        if (!env('NOT_INDEX_WEBSITE')) {
            header('X-Robots-Tag: noindex,nofollow'); // Заголовок запрещающий индексацию сайта
        }

        // Дополнительное приложение Debugbar
        if (!config('add.debugbar')) {
            \Debugbar::disable();
        }

        // Мобильный или планшет
        $detect = new \Mobile_Detect();
        $isMobile = $detect->isMobile();
        $isTablet = $detect->isTablet();
        Main::set('isMobile', $isMobile);
        Main::set('isTablet', $isTablet);

        // Добавление настроек в контейнет сайта
        $this->setting();

        // Глобальные переменные для видов
        $this->views();
    }


    private function setting()
    {
        /*
         * Использовать: echo Main::site('name');
         *
         * Дополнительные варианты:
         * echo Main::get('settings')['site_name'];
         * echo Main::$registry->get('settings')['name'];
         */
        if (cache()->has('settings_for_site')) {
            $settings = cache()->get('settings_for_site');

        } else {
            $settings = DB::table('settings')->get();

            // Кэшируется запрос
            cache()->forever('settings_for_site', $settings);
        }

        if (!empty($settings)) {
            $part = [];

            foreach ($settings as $v) {
                $part[$v->title] = $v->value;
            }
            Main::$registry->set('settings', $part);
        }
    }


    // ГЛОБАЛЬНЫЕ ПЕРЕМЕННЫЕ ДЛЯ ВИДОВ
    private function views()
    {
        $siteName = Main::site('name') ?? env('APP_NAME');

        // Если не вызван метод \App\Helpers\App\setMeta(), то по-умолчанию мета: title - название сайта, тег description - пустой
        View::share('getMeta', "<title>{$siteName}</title>\n\t<meta name='description' content=''>\n");

        View::share('isMobile', Main::get('isMobile'));
        View::share('isTablet', Main::get('isTablet'));

        // Название папки для картинок в public
        View::share('img', env('IMG', 'img'));
    }
}
