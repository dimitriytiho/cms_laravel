<?php


namespace App\Helpers;



class Locale
{
    private $currentLocale;
    private $modulesPath;
    private $modulesLang;
    private $langPath;


    private function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->locales = config('add.locales');
        $this->modulesPath = config('modules.path');
        $this->modulesLang = config('modules.lang');
        $this->langPath = "{$this->modulesPath}/{$this->modulesLang}";
    }


    public static function translationsJson($varName = 'translations', $fileName = 'js')
    {
        $self = new self();
        $locale = $self->currentLocale;
        $langPath = $self->langPath;

        if (!empty($locale) && $langPath) {
            $file = "{$langPath}/{$locale}/{$fileName}.php";
            if (is_file($file)) {
                $part = "var $varName = ";
                $translations = require($file);
                $part .= json_encode($translations);
                //$part .= Arr::arrToJS($translations, false, true);
            }
            return $part . "\n";
        }
        return false;
    }
}
