<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Models\Main;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Services\Registry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Пагинация Bootstrap
        Paginator::useBootstrap();


        // ЗДЕСЬ ПИСАТЬ КОД, КОТОРЫЙ ЗАПУСКАЕТСЯ ПОСЛЕ ЗАГРУЗКИ ВСЕХ СЕРВИС-ПРОВАЙДЕРОВ

        // Паттерн реестр
        Main::$registry = Registry::instance();


        // Добавление настроек в контейнет сайта
        $this->setting();


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


        // Подключаем вспомогательные библиотеки из /app/Lib
        $lib = app_path('Lib');
        $functionFile = "{$lib}/function.php";
        $constructorFile = "{$lib}/constructor.php";

        if (File::isFile($functionFile)) {
            require_once $functionFile;
        }
        if (File::isFile($constructorFile)) {
            require_once $constructorFile;
        }


        // Возвращает Webp картинку в видах (если браузер поддерживает webp) с помощью коданды @webp($imagePublicPath)
        Blade::directive('webp', function ($imagePublicPath) {
            if ($imagePublicPath && \App\Modules\Admin\Helpers\Img::supportWebp()) {
                return "<?php echo \App\Modules\Admin\Helpers\Img::getWebp($imagePublicPath); ?>";
            }
            return "<?php echo $imagePublicPath; ?>";
        });


        // Добавляем Google ReCaptcha в валидатор
        Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $recaptcha = new ReCaptcha(config('add.recaptcha_secret_key'));
            $resp = $recaptcha->verify($value, request()->ip());

            return $resp->isSuccess();
        });


        // Добавляем папку для переводов, т.е. namespace для переводов
        $modulesPath = config('modules.path');
        $modulesNamespace = config('modules.namespace');
        $modulesLang = config('modules.lang');
        $this->loadTranslationsFrom("{$modulesPath}/{$modulesLang}", "{$modulesNamespace}\\{$modulesLang}");


        // Если индексирование сайта выключено
        if (config('add.not_index_website')) {
            header('X-Robots-Tag: noindex,nofollow'); // Заголовок запрещающий индексацию сайта
        }


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
        // Имя сайта
        $siteName = Main::site('name') ?: config('add.name');

        // Если не вызван метод \App\Helpers\App\setMeta(), то по-умолчанию мета: title - название сайта, тег description - пустой
        $getMeta = "<title>{$siteName}</title>\n\t<meta name='description' content=''>\n";

        // Кононический Url без Get параметров
        $cononical = Main::notPublicInURL();

        // Телефон или планшет
        $isMobile = Main::get('isMobile');
        $isTablet = Main::get('isTablet');

        // Название папки для картинок в public
        $img = config('add.img', 'img');

        view()->share(compact('siteName', 'getMeta', 'cononical', 'isMobile', 'isTablet', 'img'));
    }
}
