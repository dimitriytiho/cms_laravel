<?php


namespace App\Modules\Admin\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class App
{
    /*
     * Возвращает потомков, если они есть, по parent_id или другой колонки.
     * $id - проверяемое id записи.
     * $table - название таблицы.
     * $column - название колонки, по-умолчанию parent_id, необязательный параметр.
     */
    public static function getIdParents($id, $table, $column = 'parent_id')
    {
        if ((int)$id && Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            return DB::table($table)->select('id')->where($column, (int)$id)->get()->toArray();
            //return DB::select("select `id` from `$table` where `$column` = ?", [(int)$id]);
        }
        return false;
    }


    /*
     * Сравнивает значения одного массива с другим.
     * Возвращает true или false.
     * $arr1, $arr2 - массивы.
     */
    public static function arrayDiff($arr1, $arr2)
    {
        if ($arr1 && $arr2 && is_array($arr1) && is_array($arr2)) {
            foreach ($arr1 as $k => $v) {

                if ($v != $arr2[$k]) {
                    return true;
                }
            }
        }
        return false;
    }
}
