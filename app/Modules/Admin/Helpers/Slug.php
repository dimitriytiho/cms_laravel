<?php


namespace App\Modules\Admin\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Slug
{
    /*
     * Проверить и исправить slug если нужно.
     * $table - название таблицы.
     * $value - проверяемое значение.
     * $add - строка, которую дописать, необязательный параметр.
     * $current_id - id текущего элемента, если вы обновляйте запись, необязательный параметр.
     * $select - наименование значения, по-умолчанию slug, необязательный параметр.
     * $addSelectKey - дополнительный параметр выбора из БД, это название колонки.
     * $addSelectValue - дополнительный параметр выбора из БД, это название значение ячейки.
     */
    public static function checkRecursion($table, $value, $add = null, $current_id = null, $select = null, $addSelectKey = null, $addSelectValue = null)
    {
        $slug = $value;
        $select = $select ?: 'slug';
        if (self::checkItem($table, $value, $current_id, $select, $addSelectKey, $addSelectValue)) {
            $slug = self::uniqueItem($value, $add, $table);
        }

        if (self::checkItem($table, $slug, $current_id, $select, $addSelectKey, $addSelectValue)) {
            $slug = self::checkRecursion($table, $value, $add, $select);
        }
        return $slug;
    }


    /*
     * Возвращает 1 или 0, есть элемент в БД или нет.
     * $table - название таблицы.
     * $value - проверяемое значение.
     * $current_id - id текущего элемента, если вы обновляйте запись, необязательный параметр.
     * $select - наименование значения, по-умолчанию slug, необязательный параметр.
     * $addSelectKey - дополнительный параметр выбора из БД, это название колонки.
     * $addSelectValue - дополнительный параметр выбора из БД, это название значение ячейки.
     */
    public static function checkItem($table, $value, $current_id = null, $select = null, $addSelectKey = null, $addSelectValue = null)
    {
        $select = $select ?: 'slug';

        if ($current_id) {
            if ($addSelectKey && $addSelectValue) {
                return DB::table($table)->select($select)->where('id', '!=', $current_id)->where($select, $value)->where($addSelectKey, $addSelectValue)->count();

            } else {

                return DB::table($table)->select($select)->where('id', '!=', $current_id)->where($select, $value)->count();
            }
        }
        return DB::table($table)->select($select)->where($select, $value)->count();
    }


    /*
     * Делает уникальный slug, т.е. дописывает add к slug.
     * $slug - строка slug.
     * $add - строка, которую дописать, необязательный параметр.
     * $table - название таблицы, в которой будет slug, это для того если не передавать строку $add, тогда будет взята последняя цифра в БД плюс один, необязательный параметр.
     * $cyrillicToLatin - перевести из кириллицы в латиницу, по-умолчанию true, необязательный параметр.
     * $length - возвращаемая длина, по-умолчанию 72 символов, необязательный параметр.
     */
    public static function uniqueItem($slug, $add = null, $table = null, $cyrillicToLatin = true, $length = 78)
    {
        if (!$add && Schema::hasTable($table)) {

            $add = DB::table($table)->count() + 1;
        }
        $add = $add ?: 1;

        if ($cyrillicToLatin) {
            $slug = self::cyrillicToLatin($slug, $length);
        }
        return "{$slug}-{$add}";
    }


    /*
     * Возвращает строку в латинице из кириллицы для URL.
     * $str - строка.
     * $length - возвращаемая длина, по-умолчанию 72 символов, необязательный параметр.
     */
    public static function cyrillicToLatin($str, $length = 78)
    {
        return Str::limit(Str::slug($str), $length, '');
        /*if ($str && is_string($str)) {
            // Ограничивает кол-во символов до 40
            $str = mb_substr($str, 0, (int)$length, 'utf-8');
            // Переводит в транслит
            $str = self::replaceCyrillic($str);
            // В нижний регистр
            $str = strtolower($str);
            // Заменяет все ненужное нам на '-'
            $str = preg_replace('~[^-a-z0-9]+~u', '-', $str);
            // Удаляет начальные и конечные '-'
            return trim($str, '-');
        }
        return false;*/
    }


    public static function exceptionsName($str) {
        if ($str) {
            $str = str_replace(' ', '_', $str);
            return str_replace([':', '-', '.'], '-', $str);
        }
        return false;
    }


    /*public static function replaceCyrillic($str)
    {
        $converter = [
            'а' => 'a',   'б' => 'b',   'в' => 'v',

            'г' => 'g',   'д' => 'd',   'е' => 'e',

            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',

            'и' => 'i',   'й' => 'y',   'к' => 'k',

            'л' => 'l',   'м' => 'm',   'н' => 'n',

            'о' => 'o',   'п' => 'p',   'р' => 'r',

            'с' => 's',   'т' => 't',   'у' => 'u',

            'ф' => 'f',   'х' => 'h',   'ц' => 'c',

            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',

            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',

            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',


            'А' => 'A',   'Б' => 'B',   'В' => 'V',

            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',

            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',

            'И' => 'I',   'Й' => 'Y',   'К' => 'K',

            'Л' => 'L',   'М' => 'M',   'Н' => 'N',

            'О' => 'O',   'П' => 'P',   'Р' => 'R',

            'С' => 'S',   'Т' => 'T',   'У' => 'U',

            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',

            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',

            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',

            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        ];
        return strtr($str, $converter);
    }*/
}
