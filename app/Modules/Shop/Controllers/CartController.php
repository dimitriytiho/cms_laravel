<?php

namespace App\Modules\Shop\Controllers;

use App\Models\{Main, User};
use App\Modules\Shop\Models\Cart;
use App\Mail\SendMail;
use App\Modules\Shop\Models\Order;
use App\Modules\Shop\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Helpers\Str as HelpersStr;

class CartController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        // Таблица товаров в БД
        $table = $this->table = config('shop.product_table');

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = null;
        $route = $this->route = $request->segment(1);
        $view = $this->view = Str::snake($this->class);
        Main::set('c', $c);
        View::share(compact('class', 'c','model', 'route', 'view', 'table'));
    }



    // Страница оформления заказа /cart
    public function index(Request $request)
    {
        // Если нет вида
        Main::viewExists("{$this->viewPathModule}.{$this->c}_index", __METHOD__);

        //session()->forget('cart');
        //$cartSession = session()->has('cart') ? session()->get('cart') : [];
        //dump($cartSession);

        $cartSession = session()->has('cart') ? session()->get('cart') : [];
        $noBtnModal = true;

        $this->setMeta(__("{$this->lang}::s.cart"));
        return view("{$this->viewPathModule}.{$this->c}_index", compact('cartSession', 'noBtnModal'));
    }



    // При запросе показывает корзину в модальном окне
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $cartSession = session()->has('cart') ? session()->get('cart') : [];
            return view("{$this->viewPathModule}.{$this->c}_modal")->with(compact('cartSession'))->render();
        }
        Main::getError("{$this->class} request", __METHOD__);
    }



    // При запросе добавляет товар в корзину и показывает в модальном окне
    public function plus(Request $request, $product_id)
    {
        if ((int)$product_id) {
            $product = DB::table($this->table)->find((int)$product_id);

            // Если нет товара
            if (!$product) {
                return false;
            }


            $cart = new Cart();
            $cart->plus($product);
            $cartSession = session()->has('cart') ? session()->get('cart') : [];

            if ($request->ajax()) {
                return view("{$this->viewPathModule}.{$this->c}_modal")->with(compact('product', 'cartSession'))->render(); //->with(compact('product'))
            }
            return back(); // ->with('success', __("{$this->lang}::s.success_plus"))
        }
        Main::getError("{$this->class} request", __METHOD__);
    }



    // При запросе уменьшает кол-во товаров в корзине и показывает в модальном окне
    public function minus(Request $request, $product_id)
    {
        if ((int)$product_id) {
            $product = DB::table($this->table)->find((int)$product_id);

            // Если нет товара
            if (!$product) {
                return false;
            }


            $cart = new Cart();
            $cart->minus($product);
            $cartSession = session()->has('cart') ? session()->get('cart') : [];

            if ($request->ajax()) {
                return view("{$this->viewPathModule}.{$this->c}_modal")->with(compact('product', 'cartSession'))->render(); //->with(compact('product'))
            }
            return back(); // ->with('success', __("{$this->lang}::s.success_minus"))
        }
        Main::getError("{$this->class} request", __METHOD__);
    }



    // При запросе удаляет товар из корзины и показывает в модальном окне
    public function destroy(Request $request, $product_id)
    {
        if ((int)$product_id) {
            $product = DB::table($this->table)->find((int)$product_id);

            // Если нет товара
            if (!$product) {
                return false;
            }

            $cart = new Cart();
            $cart->destroy($product);
            $cartSession = session()->has('cart') ? session()->get('cart') : [];

            if ($request->ajax()) {
                return view("{$this->viewPathModule}.{$this->c}_modal")->with(compact('product', 'cartSession'))->render();
            }
            return back(); // ->with('success', __("{$this->lang}::s.success_destroy"))
        }
        Main::getError("{$this->class} request", __METHOD__);
    }
}
