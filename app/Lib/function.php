<?php

use App\Main;
use App\Helpers\Date;
use App\Modules\Admin\Helpers\Img;


/*
 * Возвращает распечатку массива.
 * $admin - передать true, для показа только админам.
 * $die - передать true, чтобы завершить работу скрипта.
 */
function du($arr, $admin = false, $die = false) {

    if ($admin && admin()) {
        echo '<pre>' . PHP_EOL . print_r($arr, true) . PHP_EOL . '</pre>';
        if ($die) die;

    } elseif (!$admin) {

        echo '<pre>' . PHP_EOL . print_r($arr, true) . PHP_EOL . '</pre>';
        if ($die) die;
    }
}


// Проверяет роль Админ, возвращает true или false
function admin()
{
    return auth()->check() && auth()->user()->Admin();
}


// Возвращает пространство имён для переводов
function lang() {
    $modulesNamespace = config('modules.namespace');
    $modulesLang = config('modules.lang');

    if ($modulesNamespace && $modulesLang) {
        return "{$modulesNamespace}\\{$modulesLang}";
    }
    return false;
}


/*
 * Возвращается переводную фразу, если её нет, то строку.
 * $str - строка для перевода.
 * $fileLang - имя файла с переводом, по-умолчанию t(t.php), необязательный параметр.
 */
function l($str, $fileLang = 't')
{
    if ($str) {
        $lang = lang();
        return \Lang::has("{$lang}::{$fileLang}.{$str}") ? __("{$lang}::{$fileLang}.{$str}") : $str;
    }
    return false;
}


/*
 * Возвращает дату в нужном формате.
 * $date - дата в формате: 1544636288 или 2019-07-18 13:00:00.
 * $format - формат для отображения, по-умолчанию j M Y, необязательный параметр.
 */
function d($date, $format = null) {

    if ($date || $date != '0000-00-00 00:00:00') {
        $format = $format ?? Main::site('date_format') ?: 'j M Y';

        // Считаются символы в дате и если 10, то формат 1544636288, если больше 10, то формат 2019-07-18 13:00:00
        $datetime = strlen($date) > 10;

        // Если не английский язык
        if (app()->getLocale() !== 'en') {
            $f = strpos($format, 'F'); // Полное название месяца
            $m = strpos($format, 'M'); // Сокращённое название месяца

            if ($f || $m) {

                // Пространство имён для переводов
                $lang = lang();

                // Номер месяца
                $number = $datetime ? date_format(date_create($date), 'n') : date('n', (int)$date);
                $months = Date::months();

                // Заменяется F на название месяца
                if ($f) {
                    $month = $months[$number];
                    //$month = months()[$number];
                    $format = str_replace('F', __("{$lang}::s.{$month}"), $format);

                // Заменяется M на название месяца
                } elseif ($m) {
                    $month = mb_substr( __("{$lang}::s.{$months[$number]}") , 0, 3, 'utf-8');
                    $format = str_replace('M', $month, $format);
                }
            }
        }

        return $datetime ? date_format(date_create($date), $format) : date($format, $date);
    }
    return false;
}


/*
 * Вырезаются html теги и допольнительные знаки.
 * $str - строка для обработки.
 * $only_strip_tags - передать true, если надо вырезаются только html теги без дополнительных знаков, необязательный параметр.
 */
function s($str, $only_strip_tags = null, $email = null)
{
    if (!$only_strip_tags) {
        $str = str_replace(['`', '/', '\\', '{', '}', ':', ';', '\'', '"', '[', ']', 'http', 'www.', 'HTTP', 'WWW.'], '*', $str);
        if (!$email) {
            $str = str_replace(['.com', '.ru', '.net', '.рф', '.su', '.ua', '.COM', '.RU', '.NET', '.РФ', '.SU', '.UA', '@' ], '*', $str);
        }
    }
    return trim(strip_tags($str, '<br>'));
}


/*
 * Возвращает цену в нужном формате (с пробелами после 3 символов, в конце знак валюты).
 * $price - цена, число или строка.
 * $currency - знак валюты, необязательный параметр, по-умолчанию рубль.
 */
function priceFormat($price, $currency = '&#8381;') {
    if ($price) {
        $currency = "&nbsp;<small>{$currency}</small>";
        return number_format(intval($price), 0, ',', '&nbsp;') . $currency;
    }
    return false;
}


/*
 * Возвращает картинку Webp, если она есть и браузер поддерживает Webp.
 * Если нет картинки Webp, то вернёт переданную картинку или false.
 * $imagePublicPath - путь к картинке от папки public.
 */
function webp($imagePublicPath)
{
    if ($imagePublicPath && Img::supportWebp()) {
        return Img::getWebp($imagePublicPath);
    }
    return false;
}


/*
 * Возвращает по-умолчанию строку с первой буквой в верхнем регистре.
 * $str - строка для преобразования.
 * $case - передать up - к верхнему регистру, low - к нижнему регистру, необязательный параметр.
 */
/*function l($str, $case = null)
{
    if ($case == 'up') {
        return mb_strtoupper($str, 'utf-8'); // К верхнему регистру
    } elseif ($case == 'low') {
        return mb_strtolower($str, 'utf-8'); // К нижнему регистру
    } else {
        $first = mb_substr($str, 0, 1, 'utf-8'); // Первая буква
        $last = mb_substr($str, 1, null, 'utf-8'); // Все кроме первой буквы
        $first = mb_strtoupper($first, 'utf-8');
        return  $first . $last;
    }
}*/
