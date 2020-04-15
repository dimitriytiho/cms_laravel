<?php

namespace App\Providers;

use App\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        define('MENU', app_path('Widgets/Menu/tpl'));
        define('INC_VIEW', resource_path('views/inc'));
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


        // Паттерн реестр
        App::$registry = \App\Helpers\Services\Registry::instance();

        // Если индексирование сайта выключено
        if (config('add.not_index_website')) {
            header('X-Robots-Tag: noindex,nofollow'); // Заголовок запрещающий индексацию сайта
        }

        if (!config('add.debugbar')) {
            \Debugbar::disable();
        }

        // Добавление настроек в контейнет сайта
        $this->setting();

        // Глобальные переменные для видов
        $this->views();
    }



    private function setting()
    {
        // dump(App::$registry->get('settings')['site_name']); // Пример использования
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
            App::$registry->set('settings', $part);
        }
    }


    // ГЛОБАЛЬНЫЕ ПЕРЕМЕННЫЕ ДЛЯ ВИДОВ
    private function views()
    {
        $siteName = App::$registry->get('settings')['site_name'] ?? env('APP_NAME');

        // Если не вызван метод \App\Helpers\App\setMeta(), то по-умолчанию мета: title - название сайта, тег description - пустой
        View::share('getMeta', "<title>{$siteName}</title>\n\t<meta name='description' content=''>\n");

        // Название папки для картинок в public
        View::share('img', config('add.img'));
    }
}
