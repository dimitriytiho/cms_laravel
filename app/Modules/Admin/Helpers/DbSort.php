<?php


namespace App\Modules\Admin\Helpers;


use Illuminate\Support\Facades\Schema;

class DbSort
{
    /*
     * Возвращает результат запроса к БД, с учётом поиска и сортировки, с пагинацией.
     * $queryArr - колонки для поиска.
     * $get - Get параметры из запроса.
     * $table - название таблицы.
     * $model - название модели.
     * $view - название вида.
     * $perPage - кол-во для пагинации.
     * $whereColumn - дополнительное условие выборки, название колонки, необязательный параметр.
     * $whereValue - дополнительное условие выборки, значение колонки, необязательный параметр.
     */
    public static function getSearchSort(array $queryArr, $get, $table, $model, $view, $perPage, $whereColumn = null, $whereValue = null)
    {
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;


        // Значения по-умолчанию для сортировки
        $columnSort = 'id';
        $order = 'desc';

        // Если сессия сортировки не существует, то сохраним значения по-умолчанию
        if (!session()->exists("admin_sort.{$view}")) {
            session()->put("admin_sort.{$view}.{$columnSort}", $order);
        }

        // Если передаётся через Get сортировка, то проверим есть ли такая колонка в таблице
        $get = request()->query();
        if ($get) {

            $columnSortGet = key($get);
            if (Schema::hasColumn($table, $columnSortGet)) {

                // Если есть такая колонка, то сохраним её
                $columnSort = $columnSortGet;
                $order = $get[$columnSort];
                if ($order === 'asc' || $order === 'desc') {

                    // Удалим прошлое значение
                    session()->forget("admin_sort.{$view}");

                    // Сохраним новое
                    session()->put("admin_sort.{$view}.{$columnSort}", $order);
                }
            }
        }


        // Если нужно дополнительное условие выборки
        if ($whereColumn && $whereValue) {

            // Если есть строка поиска
            if ($col && in_array($col, $queryArr) && $cell) {
                $values = $model::where($whereColumn, $whereValue)->where($col, 'LIKE', "%{$cell}%")->orderBy($columnSort, $order)->paginate($perPage);

            // Иначе выборка всех элементов из БД
            } else {
                $values = $model::where($whereColumn, $whereValue)->orderBy($columnSort, $order)->paginate($perPage);
            }

        } else {

            // Если есть строка поиска
            if ($col && in_array($col, $queryArr) && $cell) {
                $values = $model::where($col, 'LIKE', "%{$cell}%")->orderBy($columnSort, $order)->paginate($perPage);

                // Иначе выборка всех элементов из БД
            } else {
                $values = $model::orderBy($columnSort, $order)->paginate($perPage);
            }

        }

        return $values;
    }


    /*
     * Возвращает вид иконок сортировки.
     * $columnSort - название колонки сортировки.
     * $view - название вида.
     * $route - маршрут вида.
     */
    public static function viewIcons($columnSort, $view, $route)
    {
        $lang = lang();
        $langAsc = __("{$lang}::a.asc");
        $langDesc = __("{$lang}::a.desc");
        $routeAsc = route("admin.{$route}.index", "{$columnSort}=asc");
        $routeDesc = route("admin.{$route}.index", "{$columnSort}=desc");
        $activeAsc = session()->get("admin_sort.{$view}.{$columnSort}") === 'asc' ? ' active' : null;
        $activeDesc = session()->get("admin_sort.{$view}.{$columnSort}") === 'desc' ? ' active' : null;

        return <<<S
<span class="filter-icons">
    <a href="{$routeAsc}" class="material-icons{$activeAsc}" title="{$langAsc}">arrow_drop_up</a>
    <a href="{$routeDesc}" class="material-icons{$activeDesc}" title="{$langDesc}">arrow_drop_down</a>
</span>
S;
    }
}
