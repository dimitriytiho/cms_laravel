<?php


namespace App\Modules\Admin\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Img
{
    private $acceptedImages = [];

    private function __construct()
    {
        $this->acceptedImages = config('admin.acceptedImagesExt');
    }


    // Возвращает разрешенные разрешения картинок строкой '.jpg, .jpeg, .png, .gif'
    public static function acceptedImagesExt()
    {
        $self = new self();
        return $self->acceptedImages ? '.' . implode(', .', $self->acceptedImages) : false;
    }


    // Поддерживает браузер картинки Webp, возвращает true или false.
    public static function supportWebp()
    {
        $httpAccept = request()->server('HTTP_ACCEPT');
        return strpos($httpAccept, 'image/webp') !== false;
    }


    /*
     * Возвращает название картинки Webp, если она есть, если её нет, то возвращает обычную картинку.
     * $imagePublicPath - путь с название обычной картинки.
     * Название картинки Webp должно быть одинаково с обычной картинкой.
     */
    public static function getWebp($imagePublicPath)
    {
        if ($imagePublicPath) {

            // Полный путь к картинке
            $pathImg = public_path($imagePublicPath);
            if (File::isFile($pathImg)) {

                // Название картинки
                $name = class_basename($imagePublicPath);

                // Вырезаем из пути название картинки
                $path = str_replace($name, '', $imagePublicPath);

                // Получаем разрешение картинки
                $ext = pathinfo($name)['extension'] ?? null;

                // Вырезаем разрешение картинки
                $name = str_replace(".{$ext}", '', $name);

                // Название картинки webp
                $webp = "{$name}.webp";

                // Добавляем к пути название webp
                $pathWebp = public_path($path . $webp);

                // Если есть webp, то возвращаем её
                if (File::isFile($pathWebp)) {
                    return $path . $webp;
                }
            }
        }
        return $imagePublicPath;
    }


    /*
     * Удалим картинку с сервера, возвращает true или false.
     * $img - название картинки, как в БД, например /img/product/tovar_1_10-03-2020_21-28.jpeg.
     * $imgDefault - картинка по-умолчанию, если не надо её удалять, то передать, например config('admin.imgProductDefault'), необязательный параметр.
     */
    public static function deleteImg($img, $imgDefault = null)
    {
        if ($img) {
            $path = public_path() . $img;
            $ifDefault = $imgDefault && $imgDefault === $img;

            if (!$ifDefault && File::isFile($path)) {

                // Удалим картинку Webp
                $webp = self::getWebp($img);
                if ($webp !== $img) {
                    File::delete(public_path() . $webp);
                }

                // Удалим обычную картинку
                File::delete($path);
                return true;
            }
        }
        return false;
    }


    /*
     * Удалим с сервера картинки галереи, принадлежащии одному элементу, к примеру товару, возвращает true или false.
     * $table - название таблице, в которой названия картинок.
     * $elementName - название элемента в таблице, к примеру product_id.
     * $elementID - id элемента, для которого картинки.
     */
    public static function deleteImgAll($table, $elementName, $elementID)
    {
        if ($table && $elementName && $elementID && Schema::hasTable($table) && Schema::hasColumn($table, $elementName)) {

            $images = DB::table($table)->where($elementName, (int)$elementID)->pluck('img');
            $images = $images->toArray();

            if ($images) {
                foreach ($images as $img) {
                    self::deleteImg($img);
                }
                return true;
            }
        }
        return false;
    }
}
