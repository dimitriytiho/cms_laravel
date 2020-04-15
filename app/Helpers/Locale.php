<?php


namespace App\Helpers;



class Locale
{
    private $currentLocale;

    private function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->locales = config('add.locales');
    }


    public static function translationsJson($varName = 'translations', $fileName = 'js')
    {
        $self = new self();
        $locale = $self->currentLocale;
        if (!empty($locale)) {
            $file = resource_path("lang/$locale/$fileName.php");
            if (is_file($file)) {
                $part = "var $varName = ";
                $translations = require($file);
                $part .= Arr::arrToJS($translations, false, true);
            }
            return $part . "\n";
        }
        return false;
    }


    /*public static function translationsAdminJson()
    {
        $self = new self();
        $locales = $self->locales;
        if (!empty($locales[0])) {
            $part = '';
            $part .= "var translations = {";
            foreach ($locales as $v) {
                $file = resource_path("lang/$v/a.php");
                if (is_file($file)) {
                    $part .= "$v: ";
                    $translations = require($file);
                    $part .= Arr::arrToJS($translations, false, true) . ',';
                }
            }
            $part = rtrim($part, ',') . "}\n";
            return $part;
        }
        return false;
    }*/
}
