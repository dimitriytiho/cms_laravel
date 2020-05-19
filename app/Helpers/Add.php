<?php


namespace App\Helpers;


class Add
{
    /*
     * Если в строке запроса содержится строка.
     * Возвращает true или false.
     * $str - строка.
     */
    public static function inRequestStr($str)
    {
        if ($str) {
            $request = request()->path();
            if (strpos($request, $str) !== false) {
                return true;
            }
        }
        return false;
    }
}
