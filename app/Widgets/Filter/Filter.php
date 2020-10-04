<?php


namespace App\Widgets\Filter;


use App\Models\Main;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Shop\Helpers\Filter as helpersFilter;

class Filter
{
    // Настройки виджета
    private $options = [
        'tpl' => 'default', // Передать название шаблона из папки tpl
        'cache' => true, // Если не нужно кэшировать, то передать false
        'cacheNameGroups' => 'filter_groups', // При кэшировании передать название кэша
        'cacheNameValues' => 'filter_values', // При кэшировании передать название кэша
        'table_groups' => 'filter_groups', // Название таблицы c группами
        'table_values' => 'filter_values', // Название таблицы с названиями фильтров
    ];

    private $groups;
    private $values;



    // Входной основной метод
    public static function init($params = [])
    {
        $self = new self();

        $self->getParams($params);
        $self->run();
        $self->toTemplate();
    }



    // Заполняем настройки
    private function getParams($params = [])
    {
        $data = [];
        foreach ($this->options as $propName => $value) {
            isset($params[$propName]) ? $data[$propName] = $params[$propName] : $data[$propName] = $value;
        }

        // Назначим шаблон html
        $data['tpl'] = is_file(__DIR__ . "/tpl/{$data['tpl']}.php") ? __DIR__ . "/tpl/{$data['tpl']}.php" : __DIR__ . '/tpl/default.php';

        $this->options= $data;
        return;
    }


    // Получаем данные
    private function run()
    {
        $params = $this->options;

        // Если не существует таблица
        if (!Schema::hasTable($params['table_groups']) || !Schema::hasTable($params['table_values'])) {
            return false;
        }

        // Получаем все группы
        $this->groups = self::getGroups();

        // Получаем все фильтры
        $values = self::getFilters();


        // Формируем нужный массив с фильтрами
        if ($values) {
            foreach ($values as $key => $filter) {
                $this->values[$filter->parent_id][$filter->id] = $filter->value;
            }
        }

        return true;
    }


    public static function getGroups()
    {
        $self = new self();
        $params = $self->options;

        // Получаем из кэша для групп
        if ($params['cache'] && cache()->has($params['cacheNameGroups'])) {
            $groups = cache()->get($params['cacheNameGroups']);

        } else {

            // Запрос в БД
            $groups = DB::table($params['table_groups'])->get()->toArray();

            // Кэшируется запрос
            if ($params['cache']) {
                cache()->forever($params['cacheNameGroups'], $self->groups);
            }
        }
        return $groups;
    }


    public static function getFilters()
    {
        $self = new self();
        $params = $self->options;

        // Получаем из кэша для фильтров
        if ($params['cache'] && cache()->has($params['cacheNameValues'])) {
            $values = cache()->get($params['cacheNameValues']);

        } else {

            // Запрос в БД
            $values = DB::table($params['table_values'])->get()->toArray();

            // Кэшируется запрос
            if ($params['cache']) {
                cache()->forever($params['cacheNameValues'], $self->groups);
            }
        }

        return $values;
    }


    private function toTemplate()
    {
        $params = $this->options;

        // Получаем выбранные фильтры
        $filterActive = helpersFilter::getFilter();
        $filterActive = $filterActive ? explode(',', $filterActive) : null;

        ob_start();
        include $params['tpl'];
        echo ob_get_clean();
    }
}
