<?php


namespace App\Helpers;


class Arr
{
    /*
     * Возвращает объект для JS из вложенного массива php.
     * $arr - вложенный массив.
     */
    public static function doubleArrToObjJS($arr, $var_name = null)
    {
        if (!empty($arr) && is_array($arr)) {
            $JS = "var $var_name = {";
            foreach ($arr as $k => $v) {
                $JS .= "{$k}: " . self::arrToJS($v, null, false) . ',';
            }
            $JS = rtrim($JS, ',') . "}\n";
            return $JS;
        }
        return false;
    }


    /*
     * Возвращает массив для JS из массива php.
     * $arr - массив.
     * $var_name - название переменной для JS, необязательный параметр.
     * $obj - передайте true, если надо получить объект.
     * $only_name - передайте false, если надо полное имя файла с расширением, необязательный параметр.
     */
    public static function arrToJS($arr, $var_name = null, $obj = false, $only_name = false)
    {
        $l = $obj ? '{' :'[';
        $r = $obj ? '}' :']';
        if (!empty($arr) && is_array($arr)) {
            $part = '';
            $part .= $var_name ? "var $var_name = $l" : $l;
            foreach ($arr as $k => $v) {
                if ($only_name) {
                    $v = pathinfo($v)['filename'];
                }
                $part .= $obj ? "'{$k}': '{$v}'," : "'{$v}',";
            }
            $part = rtrim($part, ',') . $r;
            return $part;
        }
        return false;
    }
}
