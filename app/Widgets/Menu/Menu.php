<?php


namespace App\Widgets\Menu;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Menu
{
    // Настройки виджета
    private $options = [

        // Общие
        'tpl' => 'default', // Передать название шаблона из папки tpl, без .php
        'submenu' => null, // Передать id, например $page->id
        'cache' => true, // Если не нужно кэшировать, то передать false
        'cacheName' => '', // При кэшировании передать название кэша или оно возьмётся из название tpl
        'data' => null, // Можно передать готовые данные SQL запроса для обработки

        // Запрос Sql
        'table' => 'menu', // Название таблицы
        'where' => [], // [['id', 7]] К примеру где id = 7 или множественно [['id', '7'], ['accept', '1'],]
        'orderBy' => 'sort',
        'sort' => 'asc',
        'sql' => '', // Передать частный sql запрос, так делается дополнительный цикл, при этом не будут работать настройки: table, where, orderBy, sort ('SELECT * FROM menus ORDER BY id DESC')

        // Вывод для Html
        'container' => 'ul',
        'class' => null,
        'classLi' => null,
        'classLink' => null,
        'classActive' => 'active',
        'attrs' => null, // Передать строкой или массив атрибутов html, например ['id' => 'menu_mobile'], будет id="menu_mobile"
        'before' => '', // Добавить первый пункт, к примеру для select можно передать перевый option
        'after' => '', // Добавить последний пункт
    ];




    // Входной основной метод
    public static function init($params = [])
    {
        $self = new self();

        // Параметры виджета по умолчанию
        $option = $self->options;

        // Заполняем пользовательские данные в параметры
        $self->getParams($params);

        // Запускаем работу виджета
        $self->run();

        // Обнуляем параметры виджета
        $self->options = $option;
    }




    // Заполняем настройки
    private function getParams($params = [])
    {
        $data = [];
        $name = class_basename(__CLASS__);

        foreach ($this->options as $propName => $value) {
            $data[$propName] = isset($params[$propName]) ? $params[$propName] : $value;
        }

        // Если не передаётся cacheName, то будет имя класса, имя таблицы, имя шаблона меню tpl
        $data['cacheName'] = $data['cacheName'] ?: "{$name}_{$data['table']}_{$data['tpl']}";

        // Назначим шаблон html
        $data['tpl'] = is_file(__DIR__ . "/tpl/{$data['tpl']}.php") ? __DIR__ . "/tpl/{$data['tpl']}.php" : __DIR__ . '/tpl/default.php';

        $this->options = $data;
        return;
    }


    // Получаем данные
    private function run()
    {
        $params = $this->options;

        // Если передаются данные, то не делаем запросов в БД
        if ($params['data']) {
            $data = $params['data'];

        } else {

            // Если не существует таблица
            if (!Schema::hasTable($params['table'])) {
                return false;
            }

            // Получаем данные из кэша
            $data = $params['cache'] && cache()->has($params['cacheName']) ? cache()->get($params['cacheName']) : null;

            // Если не получили из кэша делаем запрос в БД
            if (!$data) {
                if ($params['sql']) {
                    $data = DB::select($params['sql']);

                    // Ключи массива заменяем на id
                    if (!empty($data)) {
                        foreach ($data as $v) {
                            $dataSql[$v->id] = $v;
                        }
                        $data = $dataSql;
                    }

                } else {

                    $data = DB::table($params['table'])
                        ->where($params['where'])
                        ->orderBy($params['orderBy'], $params['sort'])
                        ->get()
                        ->keyBy('id');
                }
            }
        }

        // Если нет данных
        if (!$data) {
            return false;
        }

        // Кэшируем данные
        if (!$params['data'] && $params['cache']) {
            cache()->forever($params['cacheName'], $data);
        }

        $tree = self::getTree($data);
        $html = self::getMenuHtml($tree);
        self::output($html);
        return true;
    }


    // Строим дерево
    private function getTree($data)
    {
        $params = $this->options;
        $tree = [];

        // Если данные не пусты
        $check = is_object($data) && $data->isNotEmpty() || is_array($data) && array_filter($data);
        if ($check) {

            // Строим дерево
            foreach ($data as $key => &$node) {
                $parent_id = $node->parent_id ?? 0;
                $id = $node->id;

                if ($parent_id == 0) {
                    $tree[$id] = &$node;

                } else {

                    if (!empty($data[$parent_id]->childs->$id)) {
                        $data[$parent_id]->childs->$id = &$node;
                    }
                }
            }
        }

        // Если нужно получить часть меню
        $subId = (int)$params['submenu'];
        if ($subId) {

            // Если есть вложенные элементы вернём их
            return $tree[$subId]->childs ?? [];

        }

        // Возвращаем дерево
        return $tree;
    }


    // В цикле вызываем передаём данные в шаблон Html
    private function getMenuHtml($tree, $tab = '')
    {
        $str = '';
        $i = 0;
        foreach ($tree as $id => $item) {
            $i++;
            $str .= self::toTemplate($item, $tab, $id, $i);
        }
        return $str;
    }


    // В шаблон передаются массив с данными из БД ($item), $tab - показывает вложенность (к примеру можно использовать дефис -, $id - id, $i - счётчик)
    private function toTemplate($item, $tab, $id, $i)
    {
        $params = $this->options;

        ob_start();
        include $params['tpl'];
        return ob_get_clean();
    }


    // Редактируем Html
    private function output($html)
    {
        $params = $this->options;
        $attrs = '';

        if ($params['attrs'] && is_array($params['attrs'])) {
            foreach ($params['attrs'] as $k => $v) {
                $attrs .= "{$k}='{$v}' ";
            }
        } else {
            $attrs = $params['attrs'];
        }

        $params['class'] = $params['class'] ? " class='{$params['class']}'" : null;
        echo $params['container'] ? "<{$params['container']}{$params['class']} {$attrs}>\n" : null;
        echo $params['before'];
        echo $html;
        echo $params['after'];
        echo $params['container'] ? "</{$params['container']}>\n" : null;
    }
}
