<?php


namespace App\Helpers;


class Locale
{
    private $currentLocale;
    private $locales;
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


    /**
     *
     * @return string
     *
     * Возвращает строку, в формате json c переводами.
     * $varName - название переменной для JS, по-умолчанию translations, необязательный параметр.
     * $fileName - имя файла из lang папки (например app/Modules/lang/en/js.php), по-умолчанию js, необязательный параметр.
     */
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
