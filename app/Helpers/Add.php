<?php


namespace App\Helpers;

use Illuminate\Support\Facades\File;

class Add
{
    /*
     * Возвращает включен сайт или выключен, false или true.
     * Чтобы выключить сайт запишите в /storage/site_off.php что-нибудь, например: 1.
     */
    public static function siteOff()
    {
        $file = config('add.site_off_file');
        $content = null;
        if (File::isFile($file)) {
            $content = File::get($file);
        }
        return config('add.site_off') || $content;
    }


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
