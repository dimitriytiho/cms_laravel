<?php


namespace App\Widgets\Menu;


use Illuminate\Support\Facades\DB;

class Menu
{
    // Настройки
    protected $tpl; // Для указания пути используйте константу MENU, передать название шаблона из папки tpl
    protected $submenu; // Передать $page->id
    protected $cache = true; // Если не нужно кэшировать, то передать false
    protected $cacheName = ''; // При кэшировании передать название кэша или оно возьмётся из название tpl

    // Настройки запроса
    protected $table = 'menus'; // Необходимо, чтобы была модель, передаваемой таблицы
    protected $where = []; // ['id', '7'] К примеру где id = 7
    protected $orderBy = 'id';
    protected $sort = 'desc';
    protected $sql = ''; // Передать частный sql запрос, так делается дополнительный цикл, при этом не будут работать настройки: table, where, orderBy, sort ('SELECT * FROM menus ORDER BY id DESC')

    // Настройки вывода
    protected $container = 'ul';
    protected $class = null;
    protected $attrs = []; // Массив атрибутов
    protected $prepend = ''; // Для select можно передать перевый option
    protected $js = null; // При использовании js не будут работать настройки: container, class, attrs, prepend

    // Служебные
    private $data;
    private $tree;
    public $menuHtml;


    public function __construct($options = [])
    {
        $this->getOptions($options);
        $this->tpl = is_file("{$this->tpl}.php") ? "{$this->tpl}.php" : __DIR__ . '/tpl/default.php';
        $this->run();
    }


    public function getOptions($options)
    {
        foreach ($options as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }


    protected function run()
    {
        // Если не передаётся cacheName, то будет имя шаблона меню tpl
        $this->cacheName = $this->cacheName ?: $this->tpl;
        $this->menuHtml = cache()->has($this->cacheName) ? cache()->get($this->cacheName) : null;

        if (!$this->menuHtml) {

            if (!$this->data) {
                if ($this->sql) {
                    $this->data = DB::select($this->sql);
                    if (!empty($this->data)) {
                        foreach ($this->data as $v) {
                            $data[$v->id] = $v;
                        }
                        $this->data = $data;
                    }

                } else {

                    if ($this->where) {
                        $this->data = DB::table($this->table)->where($this->where[0], $this->where[1])->orderBy($this->orderBy, $this->sort)->get()->keyBy('id');

                    } else {

                        $this->data = DB::table($this->table)->orderBy($this->orderBy, $this->sort)->get()->keyBy('id');
                    }
                }
            }

            // Если в таблице БД нет ничего
            if (!$this->data) {
                return false;
            }

            $this->tree = $this->getTree();
            $this->menuHtml = $this->getMenuHtml($this->tree);
            if ($this->cache) {
                cache()->forever($this->cacheName, $this->menuHtml);
            }
        }
        $this->output();
    }


    protected function output()
    {
        if ($this->js) {
            $menu = rtrim($this->menuHtml, ',');
            echo 'var menuJS = {' . $menu . "}\n";

        } else {

            $attrs = '';
            if (!empty($this->attrs)) {
                foreach ($this->attrs as $k => $v) {
                    $attrs .= " $k='$v' ";
                }
            }
            $this->class = $this->class ? " class='{$this->class}'" : null;
            echo $this->container ? "<{$this->container}{$this->class}{$attrs}>\n" : null;
            echo $this->prepend;
            echo $this->menuHtml;
            echo $this->container ? "</{$this->container}>\n" : null;
        }
    }


    protected function getTree()
    {
        $tree = [];
        if ($this->submenu) {
            $tree = $tree[$this->submenu]->childs;

        } else {

            $data = $this->data;
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


    protected function getMenuHtml($tree, $tab = '')
    {
        $str = '';
        $i = 0;
        foreach ($tree as $id => $item) {
            $i++;
            $str .= $this->toTemplate($item, $tab, $id, $i);
        }
        return $str;
    }


    // В шаблон передаются массив с данными из БД ($item), $tab - показывает вложенность (к примеру можно использовать дефис -, $id - id, $i - счётчик)
    protected function toTemplate($item, $tab, $id, $i)
    {
        ob_start();
        include $this->tpl;
        return ob_get_clean();
    }
}
