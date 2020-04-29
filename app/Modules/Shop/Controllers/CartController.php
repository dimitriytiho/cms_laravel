<?php

namespace App\Modules\Shop\Controllers;

use App\App;
use App\Modules\Shop\Models\Cart;
use App\Mail\SendMail;
use App\Modules\Shop\Models\Order;
use App\Modules\Shop\Models\OrderProduct;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

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
        App::set('c', $c);
        View::share(compact('class', 'c','model', 'route', 'view', 'table'));
    }



    // Страница оформления заказа /cart
    public function index(Request $request)
    {
        // Если нет вида
        App::viewExists("{$this->viewPathModule}.{$this->c}_index", __METHOD__);

        //session()->forget('cart');
        //$cartSession = session()->has('cart') ? session()->get('cart') : [];
        //dump($cartSession);

        $cartSession = session()->has('cart') ? session()->get('cart') : [];
        $noBtnModal = true;

        $this->setMeta(__("{$this->lang}::sh.cart"));
        return view("{$this->viewPathModule}.{$this->c}_index", compact('cartSession', 'noBtnModal'));
    }



    // При запросе показывает корзину в модальном окне
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $cartSession = session()->has('cart') ? session()->get('cart') : [];
            return view("{$this->viewPathModule}.{$this->c}_modal")->with(compact('cartSession'))->render();
        }
        App::getError("{$this->class} request", __METHOD__);
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
            return back(); // ->with('success', __("{$this->lang}::sh.success_plus"))
        }
        App::getError("{$this->class} request", __METHOD__);
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
            return back(); // ->with('success', __("{$this->lang}::sh.success_minus"))
        }
        App::getError("{$this->class} request", __METHOD__);
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
            return back(); // ->with('success', __("{$this->lang}::sh.success_destroy"))
        }
        App::getError("{$this->class} request", __METHOD__);
    }



    // Принимает post форму оформления заказа
    public function makeOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $passwordDefault = '$2y$10$0v6wawOOs/cwp.wAPmbJNe4q3wUSnBqfV7UQL7YbpTtJE0dJ8bMKK'; // 123321q - такой пароль по-умолчанию у пользователей со статусом guest (не зарегистрированный пользователь)

            // Валидация
            $rules = [
                'name' => 'required',
                'tel' => 'required',
                'email' => 'required|email',
                'address' => 'required',
                'accept' => 'accepted',
            ];

            $this->validate($request, $rules);

            // Данные пользователя
            $dataUser['name'] = s($data['name']);
            $dataUser['tel'] = s($data['tel']);
            $dataUser['email'] = s($data['email'], null, true);
            $dataUser['address'] = s($data['address']);

            // Обшие данные пользователя, которые отправляем письмом
            $dataUserMail = $dataUser;

            $user = new User();
            $noChangeUser = null;

            // Проверяем существует ли такой пользователь
            $issetUser = $user->getUser($dataUser['email']);
            if ($issetUser) {
                $userId = $issetUser->id;

                // Проверяем не админ ли
                $noChangeUser = $user->getAdmin($issetUser);

                // Если текущий пользователь не админ, то обновим его данные
                if (!$noChangeUser) {
                    $dataUser['ip'] = $request->ip();

                    $issetUser->fill($dataUser);
                    $issetUser->update();
                }

            } else {

                // Если не существует пользователя, то созданим нового
                $dataUser['role_id'] = array_search('guest', config('admin.user_roles')) ?: 2; // Устанавливается роль guest
                $dataUser['password'] = $passwordDefault;
                $dataUser['accept'] = $data['accept'] ? '1' : '0';
                $dataUser['ip'] = $request->ip();

                $user->fill($dataUser);
                if ($user->save()) {
                    $userId = $user->id;

                } else {

                    // Запишем ошибку в лог файл
                    App::getError($this->class, __METHOD__, false);
                }
            }


            // Данные для таблицы orders
            $qty = session()->has('cart.qty') ? (int)session()->get('cart.qty') : null;
            $sum = session()->has('cart.sum') ? session()->get('cart.sum') : null;

            $dataOrder['user_id'] = $userId;

            if (!empty($data['message'])) {
                $dataOrder['message'] = s($data['message']);
            }
            if (!empty($data['delivery'])) {
                $dataOrder['delivery'] = s($data['delivery']);
            }
            if (!empty($data['delivery_sum'])) {
                $dataOrder['delivery_sum'] = s($data['delivery_sum']) ;
            }
            if (!empty($data['discount'])) {
                $dataOrder['discount'] = s($data['discount']);
            }
            if (!empty($data['discount_code'])) {
                $dataOrder['discount_code'] = s($data['discount_code']);
            }

            $dataOrder['qty'] = $qty;
            $dataOrder['sum'] = $sum;
            $dataOrder['ip'] = $request->ip();

            $order = new Order();
            $order->fill($dataOrder);

            //$method = Str::kebab(__FUNCTION__); // Из contactUs будет contact-us
            if ($order->save()) {
                $orderId = $order->id;
                $data['date'] = App::get('settings')['date_format_admin'] ?? 'd.m.Y H:i';

                // Данные для таблицы order_product
                $cart = session()->has('cart') ? session()->get('cart') : [];
                if ($cart) {
                    $products = [];
                    $i = 0;
                    foreach ($cart as $productId => $product) {
                        if (is_int($productId)) {
                            $products[$i]['order_id'] = $orderId;
                            $products[$i]['product_id'] = $productId;
                            if (!empty($product['discount'])) {
                                $products[$i]['discount'] = $product['discount'];
                            }
                            $products[$i]['qty'] = (int)$product['qty'];
                            $products[$i]['sum'] = $product['price'] * (int)$product['qty'];
                            $i++;
                        }
                    }

                    // Вставим товары в таблицу
                    if ($products) {
                        DB::table('order_product')->insert($products);
                    }

                    // Очистим корзину, удалив сессию
                    session()->forget('cart');
                }


                // Письмо пользователю
                try {
                    $title = __("{$this->lang}::s.You_placed_order") . config('add.domain');
                    $body = __("{$this->lang}::s.Your_order_was_successfully_received");

                    // Отправить письмо
                    Mail::to($dataUser['email'])
                        ->send(new SendMail($title, $body, $cart, $this->c));

                } catch (\Exception $e) {
                    App::getError("Error sending email admin: $e", __METHOD__, false);
                }

                // Письмо администратору
                try {
                    $title = __("{$this->lang}::s.An_order_has_been_placed", ['order_id' => __("{$this->lang}::{$orderId}")]) . config('add.domain');
                    $email_admin = \App\Helpers\Str::strToArr(App::get('settings')['admin_email'] ?? null);

                    // Данные пользователя
                    $body = null;
                    if (!empty($dataUserMail)) {
                        $body = view('mail.table_form')
                            ->with(['values' => $dataUserMail])
                            ->render();
                    }

                    // Отправить письмо
                    Mail::to($email_admin)->
                    send(new SendMail($title, $body, $cart, $this->c));

                } catch (\Exception $e) {
                    App::getError("Error sending email admin: $e", __METHOD__, false);
                }


                // Сообщение об успехе
                session()->put('success', __("{$this->lang}::sh.order_successfully"));
                return redirect()->route('index');
            }
        }
        // Сообщение что-то пошло не так
        App::getError("{$this->class} request", __METHOD__);
    }
}
