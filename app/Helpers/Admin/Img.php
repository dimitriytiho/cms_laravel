<?php


namespace App\Helpers\Admin;



use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Img
{
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
