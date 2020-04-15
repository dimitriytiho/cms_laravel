<?php


namespace App\Helpers;


class Str
{
    /*
     * Возвращается массив.
     * $str - строка, в которой через запятую написаны слова. Эти слова разбиваются по запятой или другому делителю.
     * $delimiter - делитель, необязательный параметр, , по-умолчанию.
     */
    public static function strToArr($str, $delimiter = ',')
    {
        if ($str) {
            $str = str_replace(' ', '', $str);
            return explode($delimiter, $str);
        }
        return false;
    }


    // Принимает массив, далее он собирается в строку по запятой и возвращается строка
    public static function arrToStr($arr)
    {
        if ($arr && is_array($arr)) {
            $str =  implode(', ', $arr);
            return rtrim($str, ', ');
        }
        return false;
    }


    /*
     * Сегмент от строки.
     * $str - строка, которая разбивается по делителю на сегменты.
     * $delimiter - делитель, необязательный параметр, , по-умолчанию.
     * $segment - номер сегмента, который нужно вернуть, необязательный параметр.
     */
    public static function strToSegment($str, $segment = 0, $delimiter = '/')
    {
        if ($str) {
            $arr = explode($delimiter, $str);
            return $arr[(int)$segment] ?? false;
        }
        return false;
    }


    // Заменяет html сущности тире и пробел на эти знаки
    public static function removeTag($str)
    {
        return str_replace(['&#8209;', '&nbsp;'], ['-', ' '], $str);
    }


    // Преобразовать строку в snake_case из snake-case
    public static function snakeCase($str)
    {
        return str_replace('-', '_', $str);
    }
}
