<?php


namespace App\Modules\Page\Controllers;

use App\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class SearchController extends AppController
{
    // Pages используется по-умолчанию
    private $tableSearch = 'pages';

    // Page используется по-умолчанию
    private $routeSearch = 'page';



    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->class);
        Main::set('c', $c);
        View::share(compact('class', 'c', 'view'));
    }


    public function index(Request $request)
    {
        $query = s($request->query('s'));
        $values = null;

        if ($query) {
            Main::set('search_query', $query);
            $perPage = config('add.pagination');




            // Если используется несколько таблиц, то добавить SQL запрос
            /*$unionProducts = DB::table('products')
                ->select([DB::raw("'product'"), 'id', 'title', 'slug'])
                ->where('status', $this->statusActive)
                ->where('title', 'LIKE', "%{$query}%");*/


            $values = DB::table($this->tableSearch)

                // Если используется несколько таблиц, то добавить эту строку
                //->union($unionProducts)




                ->select([DB::raw("'{$this->routeSearch}' as route"), 'id', 'title', 'slug'])
                ->where('status', $this->statusActive)
                ->where('title', 'LIKE', "%{$query}%")
                ->paginate($perPage);
        }

        Main::viewExists("{$this->viewPathModule}.{$this->view}_index", __METHOD__);
        $title = __("{$this->lang}::a.search");
        $this->setMeta($title);
        return view("{$this->viewPathModule}.{$this->view}_index", compact('title', 'values'));
    }


    public function js(Request $request)
    {
        if ($request->ajax()) {
            $query = s($request->get('query'));

            if ($query) {

                // Если используется несколько таблиц, то добавить SQL запрос
                /*$unionProducts = DB::table('products')
                    ->select([DB::raw("'product'"), 'id', 'title', 'slug'])
                    ->where('status', $this->statusActive)
                    ->where('title', 'LIKE', "%{$query}%");*/


                $values = DB::table($this->tableSearch)

                    // Если используется несколько таблиц, то добавить эту строку
                    //->union($unionProducts)



                    ->select([DB::raw("'{$this->routeSearch}' as route"), 'id', 'title', 'slug'])
                    ->where('status', $this->statusActive)
                    ->where('title', 'LIKE', "%{$query}%")
                    ->limit('10')
                    ->get();
                return $values->toJson();
            }
            die;
        }
        Main::getError("{$this->class} request", __METHOD__);
    }
}
