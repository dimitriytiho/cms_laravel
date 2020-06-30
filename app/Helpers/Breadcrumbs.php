<?php


namespace App\Helpers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class Breadcrumbs
{
    /*

     // Пример использования для метода в контроллере:
     $breadcrumbs = $this->breadcrumbs
            ->start(['url1' => 'title1']) // или null
            ->end(['url9' => 'title9'])
            ->get();

    // Для метода show(), т.е. для динамических элементов:
    $breadcrumbs = $this->breadcrumbs
            ->values($this->table)
            ->get($values->id, 'note');

    // Для товаров, когда используются категории, должна быть связь в модели:
    $breadcrumbs = $this->breadcrumbs
            ->values('categories')
            ->end([
                route($this->route, $values->slug) => $values->title
            ])
            ->get($values->category[0]->id, 'category');
     */


    // Первый элемент хлебных крошек
    private $start = [
        'link' => '/',
        'title' => 'home',
        'end' => false,
    ];
    private $breadcrumbs = [];
    private $values;


    //public function __construct(){}


    /*
     * Возвращаем массив хлебных крошек.
     * $id - текущего элемента, для динамических элементов, необязательный параметр.
     * $route - названия роута для элементов, по-умолчанию page, необязательный параметр.
     * $parentColumn - колонка в базе данных для родителя, по-умолчанию parent_id, необязательный параметр.
     */
    public function get($id = null, string $route = 'page', string $parentColumn = 'parent_id')
    {
        if ($id && $parentColumn && $route && $this->values) {
            foreach ($this->values as $keyId => $item) {
                if (isset($this->values[$id])) {

                    $this->breadcrumbs[] = [
                        'link' => Route::has($route) ? route($route, $this->values[$id]->slug) : '/',
                        'title' => $this->values[$id]->title,
                        'end' => false,
                    ];

                    if ($id != $this->values[$id]->$parentColumn) {
                        $id = $this->values[$id]->$parentColumn;

                    } else break;
                } else break;
            }
        }

        // Первый элемент
        if ($this->start) {
            array_push($this->breadcrumbs, $this->start);
        }

        // Отмечаем последний элемент
        if (!empty($this->breadcrumbs[0])) {
            $this->breadcrumbs[0]['end'] = true;
        }

        // Перевернём массив
        $this->breadcrumbs = $this->breadcrumbs ? array_reverse($this->breadcrumbs) : [];

        return $this->breadcrumbs;
    }


    // Последний элемент хлебных крошек, передать в массиве, например ['url' => 'title']
    public function end(array $endItem)
    {
        if ($endItem) {
            $end = [
                'link' => key($endItem),
                'title' => head($endItem),
                'end' => true,
            ];
            array_push($this->breadcrumbs, $end);
        }
        return $this;
    }


    // Первый элемент хлебных крошек, передать в массиве или null, если не нужено, например ['url' => 'title']
    public function start($startItem = true)
    {
        if (!$startItem) {
            $this->start = [];

        } elseif ($startItem && is_array($startItem)) {
            $this->start = [
                'link' => key($startItem),
                'title' => head($startItem),
                'end' => false,
            ];
        }
        return $this;
    }


    /*
     * Получает данные из базы данных и кэширует их.
     * $table - название таблицы.
     * $parentColumn - колонка в базе данных для родителя, по-умолчанию parent_id, необязательный параметр.
     */
    public function values($table, string $parentColumn = 'parent_id')
    {
        if ($table && $parentColumn) {
            $name = class_basename(__CLASS__) . '_' . class_basename($table);

            // Если есть достанем из кэша
            if (cache()->has($name)) {
                $this->values = cache()->get($name);

            // Если нет, то достанем из БД и закэшируем
            } else {
                $this->values = DB::table($table)->select('id', $parentColumn, 'title', 'slug')->get();
                $this->values = $this->values->keyBy('id');
                cache()->forever($name, $this->values);
            }
        }
        return $this;
    }
}
