<?php


namespace App\Modules\Admin\Helpers;

use Illuminate\Support\Facades\Crypt;

class Locale
{
    private $currentLocale;
    private $locales;

    public function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->locales = config('admin.locales') ?: [];
    }


    public static function excludeCurrentLocale()
    {
        $self = new self();
        $currentLocale = $self->currentLocale;
        $locales = $self->locales;
        if (in_array($currentLocale, $locales)) {
            unset($locales[array_search($currentLocale, $locales)]);
        }
        return array_values($locales);
    }


    public static function setLocaleFromCookie($request)
    {
        $self = new self();
        $currentLocale = $self->currentLocale;
        $locales = $self->locales;
        $locale = $request->cookie('loc');
        if ($locale) {
            $locale = Crypt::decryptString($locale);
            if ($locale !== $currentLocale && in_array($locale, $locales)) {
                app()->setLocale($locale);
            }
        }
    }
}
