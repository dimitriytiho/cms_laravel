<?php


namespace App\Widgets\Menu;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Menu
{
    // Настройки виджета
    private static $options = [

        // Общие
        'tpl' => 'default', // Передать название шаблона из папки tpl
        'submenu' => null, // Передать $page->id
        'cache' => true, // Если не нужно кэшировать, то передать false
        'cacheName' => '', // При кэшировании передать название кэша или оно возьмётся из название tpl

        // Запрос Sql
        'table' => 'menu', // Необходимо, чтобы была модель, передаваемой таблицы
        'where' => [], // 'id', '7' К примеру где id = 7 или множественно [['id', '7'], ['accept', '1'],]
        'orderBy' => 'id',
        'sort' => 'desc',
        'sql' => '', // Передать частный sql запрос, так делается дополнительный цикл, при этом не будут работать настройки: table, where, orderBy, sort ('SELECT * FROM menus ORDER BY id DESC')

        // Вывод для Html
        'container' => 'ul',
        'class' => null,
        'attrs' => [], // Массив атрибутов html
        'prepend' => '', // Для select можно передать перевый option
    ];




    // Входной основной метод
    public static function init($params = [])
    {
        self::getParams($params);
        self::run();
    }




    // Заполняем настройки
    private static function getParams($params = [])
    {
        $data = [];
        $name = class_basename(__CLASS__);

        foreach (self::$options as $propName => $value) {
            isset($params[$propName]) ? $data[$propName] = $params[$propName] : $data[$propName] = $value;
        }

        // Назначим шаблон html
        $data['tpl'] = is_file(__DIR__ . "/tpl/{$data['tpl']}.php") ? __DIR__ . "/tpl/{$data['tpl']}.php" : __DIR__ . '/tpl/default.php';

        // Если не передаётся cacheName, то будет имя шаблона меню tpl
        $data['cacheName'] = $data['cacheName'] ?: "{$name}_{$data['tpl']}";

        self::$options = $data;
        return;
    }


    // Получаем данные
    private static function run()
    {
        $params = self::$options;

        // Если не существует таблица
        if (!Schema::hasTable($params['table'])) {
            return false;
        }

        $menuHtml = cache()->has($params['cacheName']) ? cache()->get($params['cacheName']) : null;

        if (!$menuHtml) {

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

                $data = DB::table($params['table'])->where($params['where'])->orderBy($params['orderBy'], $params['sort'])->get()->keyBy('id');
            }

            // Если нет данных
            if (!$data) {
                return false;
            }

            $tree = self::getTree($data);
            $menuHtml = self::getMenuHtml($tree);
            if ($params['cache']) {
                cache()->forever($params['cacheName'], $menuHtml);
            }
        }
        self::output($menuHtml);
    }


    // Редактируем Html
    private static function output($menuHtml)
    {
        $params = self::$options;
        $attrs = '';

        if (!empty($params['attrs'])) {
            foreach ($params['attrs'] as $k => $v) {
                $attrs .= " {$k}='{$v}' ";
            }
        }
        $params['class'] = $params['class'] ? " class='{$params['class']}'" : null;
        echo $params['container'] ? "<{$params['container']}{$params['class']}{$attrs}>\n" : null;
        echo $params['prepend'];
        echo $menuHtml;
        echo $params['container'] ? "</{$params['container']}>\n" : null;
    }


    // Строим дерево
    private static function getTree($data)
    {
        $params = self::$options;
        $tree = [];

        if ($params['submenu']) {
            $tree = $tree[$params['submenu']]->childs;

        } else {

            foreach ($data as $key => &$node) {
                $parent_id = $node->parent_id ?? 0;
                $id = $node->id;

                if ($parent_id == 0) {
                    $tree[$id] = &$node;

                } else {
                    $data[$parent_id]->childs->$id = &$node;
                }
            }
        }
        return $tree;
    }


    // В цикле вызываем передаём данные в шаблон Html
    private static function getMenuHtml($tree, $tab = '')
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
    private static function toTemplate($item, $tab, $id, $i)
    {
        $params = self::$options;

        ob_start();
        include $params['tpl'];
        return ob_get_clean();
    }
}
