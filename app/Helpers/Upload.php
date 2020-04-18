<?php


namespace App\Helpers;


use App\App;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Upload
{
    // Запусть этот метод, чтобы обновить сайт
    public static function getUpload()
    {
        self::sitemap();
        self::robots();
        self::human();
        self::errorPage();
        //self::htaccess();

        // Обновление ключа, если в настройках change_key отмечено 1
        $changeKey = App::get('settings')['change_key'] ?? null;
        if ($changeKey) {
            self::getNewKey();
        }
    }


    /*
     * Переменые для sass из настроек.
     * Настройки для webpack sass и js.
     * При изменении sass настроек необходимо перекомпилировать стили, запустив этот метод.
     * При установке нового модуля этот метод автоматически запуститься, также можно его запустить в ручную.
     */
    public static function resourceInit()
    {
        $modulesPath = app_path(env('APP_MODULES'));

        $sassParams = config('add.scss');
        $sassParams = !empty($sassParams) && is_array($sassParams) ? $sassParams : null;
        $partSassInit = "\n// Settings SASS from " . __METHOD__ . "\n\n";

        if ($sassParams) {
            foreach ($sassParams as $k => $v) {
                $partSassInit .= "\${$k}: {$v};\n";
            }

            $fileSassInit = "{$modulesPath}/" . env('AREA_PUBLIC', 'publicly') . "/sass/config/_init.scss";
            if (\Illuminate\Support\Facades\File::exists(($fileSassInit))) {
                \Illuminate\Support\Facades\File::replace($fileSassInit, $partSassInit);
            }
        }

        // Перезапишем файл webpack.mix.js js('resources/js/app.js', 'public/js')
        $webpackPart = "const mix = require('laravel-mix');\n\n\n";
        $webpackPart .= "mix";
        $modulesPathFile = config('modules.path_file');
        $modulesArr = config('modules.modules');
        if ($modulesArr && is_array($modulesArr) && $modulesPath) {
            foreach ($modulesArr as $area => $modules) {
                $jsPublic = 'public/js';
                $cssPublic = 'public/css';
                $output = $area === 'publicly' ? 'app' : $area;

                // Главный файл js
                $jsPath = "{$modulesPath}/{$area}/js/index.js";
                if (is_file($jsPath)) {

                    $webpackPart .= ".js('{$modulesPathFile}/$area/js/index.js', '{$jsPublic}/{$output}.js')\n";
                }

                // Главный файл sass
                $sassPath = "{$modulesPath}/{$area}/sass/index.scss";
                if (is_file($sassPath)) {
                    $webpackPart .= ".sass('{$modulesPathFile}/$area/sass/index.scss', '{$cssPublic}/{$output}.css')\n";
                }

                // Файлы модулей
                if (is_array($modules)) {
                    foreach ($modules as $module => $moduleValue) {

                        // Если в настройках указан webpack
                        if (!empty($moduleValue['webpack'])) {

                            $moduleLower = Str::lower($module);

                            // Файлы модулей js
                            $jsPaths = "{$modulesPath}/{$area}/{$module}/js/index.js";
                            if (is_file($jsPaths)) {
                                $webpackPart .= ".js('{$modulesPathFile}/$area/{$module}/js/index.js', '{$jsPublic}/{$moduleLower}.js')\n";
                            }

                            // Файлы модулей sass
                            $sassPaths = "{$modulesPath}/{$area}/{$module}/sass/index.scss";
                            if (is_file($sassPaths)) {
                                $webpackPart .= ".sass('{$modulesPathFile}/$area/{$module}/sass/index.scss', '{$cssPublic}/{$moduleLower}.css')\n";
                            }
                        }
                    }
                }
            }
            $webpackPart = rtrim($webpackPart, "\n");
            $webpackPart .= ";\n";
            $webpackFile = base_path('webpack.mix.js');
            if (\Illuminate\Support\Facades\File::exists(($webpackFile))) {
                \Illuminate\Support\Facades\File::replace($webpackFile, $webpackPart);
            }
        }
    }


    // Сформировать карту сайта
    public static function sitemap()
    {
        $tables = config('add.list_of_information_block_tables');
        $list_pages = config('add.list_pages_for_sitemap_no_db');
        $active = config('add.page_statuses')[1];
        $date = date('Y-m-d');

        $r = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $r .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        if ($tables) {
            foreach ($tables as $v) {
                $values = DB::select("select slug from `$v` where status = '$active'");

                foreach ($values as $slug) {
                    $block = $v == 'pages' ? null : "$v/";
                    $r .= "\t<url>\n\t\t";
                    $r .= '<loc>' . env('APP_URL') . "/{$block}$slug->slug/</loc>\n\t\t";
                    $r .= "<lastmod>$date</lastmod>\n";
                    $r .= "\t</url>\n";
                }
            }
        }

        if ($list_pages) {
            foreach ($list_pages as $v) {
                $r .= "\t<url>\n\t\t";
                $r .= '<loc>' . env('APP_URL') . "/$v/</loc>\n\t\t";
                $r .= "<lastmod>$date</lastmod>\n";
                $r .= "\t</url>\n";
            }
        }
        $r .= '</urlset>';

        // Создать файл
        Storage::disk('public_folder')->put('sitemap.xml', $r);

        // Создать архив
        $data = implode('', file(public_path('sitemap.xml')));
        $gzdata = gzencode($data, 9);
        Storage::disk('public_folder')->put('sitemap.xml.gz', $gzdata);
    }


    // Сформировать robots.txt
    public static function robots()
    {
        $index = config('add.not_index_website');
        $disallow = config('add.disallow');
        $disallow[] = 'not-found';
        $disallow[] = '*.php$';
        $disallow[] = 'js/*.js$';
        $disallow[] = 'css/*.css$';
        $r = 'User-agent: *' . PHP_EOL;

        // Если не индексировать
        if ($index) {
            $r .= 'Disallow: /';

        // Если индексировать
        } else {
            foreach ($disallow as $v) {
                $r .= "Disallow: /$v" . PHP_EOL;
            }

            $r .= PHP_EOL . 'Host: ' . env('APP_URL') . PHP_EOL;
            $r .= 'Sitemap: ' . env('APP_URL') . '/sitemap.xml' . PHP_EOL;
            $r .= 'Sitemap: ' . env('APP_URL') . '/sitemap.xml.gz';
        }
        Storage::disk('public_folder')->put('robots.txt', $r);
    }


    // Сформировать human.txt
    public static function human()
    {
        $values = config('add.development');
        if ($values && is_array($values)) {
            $r = '';
            foreach ($values as $k => $v) {
                $r .= "$k: $v" . PHP_EOL;
            }
            $r .= 'Last update: ' . date('Y-m-d') . PHP_EOL;
            Storage::disk('public_folder')->put('humans.txt', $r);
        }
    }


    // Создаётся файл /error.php и в нём вид errors.preventive
    public static function errorPage()
    {
        if (view()->exists('errors.preventive')) {
            $r = view('errors.preventive')->render();
            Storage::disk('root')->put('error.php', $r);
        }
    }


    // Возвращает ключ для входа в admin
    public static function getKeyAdmin()
    {
        //return 'testing';
        //$key = 'testing';
        //$key = 'lnjJCb02kY7fnM3hPg';

        //dd(\Illuminate\Support\Facades\Crypt::decryptString('eyJpdiI6IlZ3XC9EclVUZUFUQXZCMHpwSWVOSjVnPT0iLCJ2YWx1ZSI6IkJwckY0bGpLTEZ6aHRkYWhrdmRRaWc9PSIsIm1hYyI6ImNjNjdmMDQ4ZTg3ZjEzOTQ0ZGFkNDdkZDJlMTMwZjYzNjkxODdmMjMyNDIwN2I4ODdkYWQxZTc5Mzg5NGZlMzUifQ=='));

        //$key = Crypt::encryptString($key); // Зашифровать
        //$key = Crypt::decryptString($key->key); // Расшифровать


        // Взязь из кэша
        if (cache()->has('key_for_site')) {
            return cache()->get('key_for_site');

        } else {

            // Запрос в БД
            $key = DB::table('uploads')->select('key')->orderBy('id', 'desc')->first();
            if (isset($key->key)) {

                // Кэшируется запрос
                cache()->forever('key_for_site', $key->key);

                return $key->key;
            }
        }
        return false;
    }


    /*
     * Сохраниться новый ключ для входа в admin.
     * $newKey - передать новый ключ, необязательный параметр, по-умолчанию сформируется ромдомный.
     * $mailAdmins - если не нужно отправлять письма администраторам и редакторам, то передать false, необязательный параметр.
     */
    public static function getNewKey($newKey = null, $mailAdmins = true)
    {
        $key = $newKey ?: Str::lower(Str::random(18));

        // Новый ключ сохраняется в БД
        $crypt = $key;
        //$crypt = Crypt::encryptString($key); // Зашифровать
        $now = time();
        $end_month = Date::timeToTimestamp(Date::timeEndDay() + 7200); // Время на 1 число месяца 2 часа ночи
        DB::insert("INSERT INTO `uploads` (`key`, date_key, date_upload) VALUES ('$crypt', '$now', '$end_month')");

        // Удалить все кэши
        cache()->flush();

        // Отправить письмо всем admin и editor
        if ($mailAdmins) {
            try {
                $emails = DB::table('users')->where('role_id', '4')->orWhere('role_id', '3')->pluck('email');
                $emails = $emails->toArray();
                if ($emails) {
                    Mail::to($emails)->send(new SendMail(__('a.Key_use_site') . config('add.domain'), $key));
                }
            } catch (\Exception $e) {
                Log::error("Error sending email $e, in " . __METHOD__);
            }
        }
    }


    // Сформировать .htaccess
    public static function htaccess()
    {
        $banned_ip = config('add.banned_ip');
        $r = '';
        if (!empty($banned_ip[0])) {
            $r .= '# ===== Closing by ip on the server =====' . PHP_EOL;
            $r .= 'Order Allow,Deny' . PHP_EOL;
            $r .= 'Allow from all' . PHP_EOL;
            $part = '';
            foreach ($banned_ip as $v) {
                $part .= "$v, ";
            }
            $part = 'Deny from ' . rtrim( $part, ', ' );
            $r .= $part . PHP_EOL . PHP_EOL;
        }

        $r .= 'addDefaultCharset utf-8' . PHP_EOL . PHP_EOL;

        $r .= 'ErrorDocument 404 /not-found' . PHP_EOL;
        $r .= 'ErrorDocument 403 /not-found' . PHP_EOL;
        $r .= 'ErrorDocument 500 /error.php' . PHP_EOL . PHP_EOL;

        $r .= 'RewriteEngine On' . PHP_EOL . PHP_EOL;

        if (config('add.protocol') == 'https') {
            $r .= '#no http and www' . PHP_EOL;
            $r .= 'RewriteCond %{HTTPS} off' . PHP_EOL;
            $r .= 'RewriteCond %{HTTP:X-Forwarded-Proto} !https' . PHP_EOL;
            $r .= 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' . PHP_EOL;
            $r .= 'RewriteCond %{HTTP_HOST} ^www\.(.*)$' . PHP_EOL;
            $r .= 'RewriteRule ^(.*)$ https://%1/$1 [R=301,L]' . PHP_EOL;

        } else {
            $r .= '#no www' . PHP_EOL;
            $r .= 'RewriteCond %{HTTP_HOST} ^www\.(.*)$' . PHP_EOL;
            $r .= 'RewriteRule ^(.*)$ http://%1/$1 [R=301,L]' . PHP_EOL . PHP_EOL;
        }

        $r .= PHP_EOL . 'RewriteCond %{REQUEST_URI} !^public' . PHP_EOL;
        $r .= 'RewriteRule ^(.*)$ public/$1 [L]' . PHP_EOL . PHP_EOL;

        // Если индексирование сайта выключено
        if (config('add.not_index_website')) {
            $r .= PHP_EOL . 'SetEnvIfNoCase User-Agent "^Googlebot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Yandex" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Yahoo" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Aport" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^msnbot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^spider" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Robot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^php" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Mail" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^bot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^igdeSpyder" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Snapbot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^WordPress" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^BlogPulseLive" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Parser" search_bot' . PHP_EOL;
        }
        Storage::disk('root')->put('.htaccess', $r);
    }
}
