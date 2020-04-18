<?php

namespace App;


use App\Helpers\Children;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    // МОДЕЛЬ APP - ВСПОМОГАТЕЛЬНАЯ СТАТИЧНАЯ МОДЕЛЬ

    /*
     * Пример использования паттерна Реестр (использовать в видах с \App\):
     * App::$registry->set('test', 'testing'); - положить
     * dump(App::$registry->get('test')); - достать
     * dump(App::$registry->getAll); - достать всё
     */
    public static $registry;


    /*
     * Упрощение вызова паттерна Реестр (использовать в видах с \App\):
     * App::set('test', 'testing'); - положить
     * dump(App::get('test')); - достать
     */
    public static function set($name, $value)
    {
        if ($name) {
            self::$registry->set($name, $value);
        }
    }
    public static function get($value)
    {
        return self::$registry->get($value) ?? null;
    }


    /*
     * Подключает файл из /resources/views/inc с название написаном в контенте ##!!!inc_name.
     * $content - если передаётся контент, то в нём будет искаться ##!!!inc_name и заменяется на файл из папки inc.
     * $values - $values5 - Можно передать данные в подключаемый файл.
     */
    public static function inc($content = null, $values = null, $values2 = null, $values3 = null, $values4 = null, $values5 = null)
    {
        if ($content) {

            $search = '##!!!'; // \w+(?=##!!!) test##!!!    (?<=##!!!)\w+ ##!!!test
            $pattern = '/(?<=' . $search . ')\w+/';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            if ($matches) {
                foreach ($matches as $v) {
                    $inc = resource_path("views/inc/{$v[0]}.php");
                    $pattern_inner = '/' . $search . $v[0] . '/';

                    if (is_file($inc)) {

                        ob_start();
                        include_once $inc;
                        $output = ob_get_clean();

                        // Замена произойдёт 1 раз;
                        $content = preg_replace($pattern_inner, $output, $content, 1);

                    } else {
                        $content = preg_replace($pattern_inner, '', $content);
                    }
                    return $content;
                }
            }
        }
        return false;
    }


    /*
     * Использовать скрипты в контенте, они будут перенесены вниз страницы.
     * $content - контент, в котором удалиться скрипты и перенести их вниз страницы.
     * В шаблоне вида получить скрипты с помощью \App\App::$registry->get('scripts').
     */
    public static function getDownScript($content)
    {
        if ($content) {
            $scripts = [];
            $pattern = "#<script.*?>.*?</script>#si";
            preg_match_all($pattern, $content, $scripts);

            if (!empty($scripts[0])) {
                self::$registry->set('scripts', $scripts[0]);
                $content = preg_replace($pattern, '', $content);
            }
            return $content;
        }
        return false;
    }


    /*
     * Метод вывода мета тегов в head.
     * $title - строка для вывода title.
     * $description - строка для вывода description, необязательный параметр.
     */
    public static function setMeta($title, $description = '', $titleSEO = '', $keywords = null)
    {
        $siteName = App::$registry->get('settings')['site_name'] ?? ' ';

        // Если нет $title, то передадим название сайта
        if (!$title) $title = $siteName;

        // Если нет $titleSEO, то передадим в неё $title
        if (!$titleSEO) $titleSEO = $title;

        // Для главной страницы сначала название сайта, а для остальных - сначала title, потом название
        $titleSEO = request()->is('/') ? "{$siteName} | {$titleSEO}" : "{$titleSEO} | {$siteName}";

        // Формируем метатеги
        $getMeta = "<title>{$titleSEO}</title>\n\t";
        $getMeta .= "<meta name=\"description\" content=\"{$description}\">\n";

        if ($keywords) {
            $getMeta .= "<meta name=\"keywords\" content=\"{$keywords}\">\n";
        }

        // Переменные передаются в виды
        View::share(compact('title'));
        View::share(compact('titleSEO'));
        View::share(compact('description'));
        View::share('getMeta', $getMeta);
    }


    /*
     * Возвращает строку: URL, Email, IP пользователя.
     * $referer - передать true, если нужно вывести страницу, с которой перешёл пользователь, необязательный параметр.
     */
    public static function dataUser($referer = null)
    {
        $email = auth()->check() && isset(auth()->user()->email) ? '. Email: ' . auth()->user()->email . '.' : null;
        if ($referer) {
            $referer = !empty(request()->server('HTTP_REFERER')) ? '. Referer: ' . request()->server('HTTP_REFERER') . '. ' : null;
        }
        return "URL: " . request()->url() . ".{$email} IP: " . request()->ip() . ". {$referer}";
    }


    /*
     * Если вида не существует, то записывает в логи ошибку и выбрасывает исключение.
     * $view - название вида (page.index).
     * $method - передать __METHOD__.
     */
    public static function viewExists($view, $method)
    {
        if (!view()->exists($view)) {
            $message = "View $view not found. " . self::dataUser() . "Error in {$method}";
            Log::critical($message);
            abort('404', $message);
            return;
        }
    }


    /*
     * Записывает в логи ошибку и выбрасывает исключение (если выбрано).
     * $message - текст сообщения.
     * $method - передать __METHOD__.
     * $abort - выбросывать исключение, по-умолчанию true, необязательный параметр.
     * $error - в каком виде записать ошибку, может быть: emergency, alert, critical, error, warning, notice, info, debug. По-умолчанию error.
     */
    public static function getError($message, $method, $abort = true, $error = 'error')
    {
        $message = "{$message}. " . self::dataUser() . "Error in {$method}";
        Log::$error($message);
        if ($abort) {
            abort('404', $message);
        }
        return;
    }
}
