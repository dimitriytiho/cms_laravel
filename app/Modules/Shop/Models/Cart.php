<?php


namespace App\Modules\Shop\Models;


class Cart
{
    /*
     * Метод добавляем в сессию товар и меняем общее кол-во и общую сумму в корзине.
     * $product - объект товара.
     * $qty - кол-во, по-умолчанию 1, необязательный параметр.
     */
    public function plus($product, $qty = 1)
    {
        if ($product) {

            // Общее кол-во в корзине
            if (session()->has('cart.qty')) {
                $cartQty = session()->get('cart.qty') + $qty;
                session()->put('cart.qty', $cartQty);
            } else {
                session()->put('cart.qty', $qty);
            }

            // Общее сумма в корзине
            if (session()->has('cart.sum')) {
                $cartSum = session()->get('cart.sum') + ($qty * $product->price);
                session()->put('cart.sum', $cartSum);
            } else {
                session()->put('cart.sum', $qty * $product->price);
            }

            // Для каждого товара
            if (session()->has("cart.{$product->id}")) {
                $qty = session()->get("cart.{$product->id}.qty") + $qty;
                session()->put("cart.{$product->id}.qty", $qty);

            } else {

                // Для нового товара
                if (config('shop.cart_elements')) {
                    foreach (config('shop.cart_elements') as $el) {
                        session()->put("cart.{$product->id}.{$el}", $product->$el);
                    }
                }
                session()->put("cart.{$product->id}.qty", $qty);
            }
            return true;
        }
        return false;
    }


    /*
     * Метод минусует из сессии товар и меняем общее кол-во и общую сумму в корзине.
     * $product - объект товара.
     * $qty - кол-во, по-умолчанию 1, необязательный параметр.
     */
    public function minus($product, $qty = 1)
    {
        if ($product) {

            // Общее кол-во в корзине
            if (session()->has('cart.qty') && session()->has('cart.sum')) {
                $productQty = session()->has("cart.{$product->id}") ? session()->get("cart.{$product->id}.qty") : 0;

                // Если кол-во товара больше 1, то будем уменьшать
                if ($productQty > 1) {

                    // Для товара
                    $productQty = $productQty - $qty;
                    session()->put("cart.{$product->id}.qty", $productQty);

                    // Общая кол-во в корзине
                    $cartQty = session()->get('cart.qty') - $qty;
                    session()->put('cart.qty', $cartQty);

                    // Общая сумма в корзине
                    $cartSum = session()->get('cart.sum') - ($qty * $product->price);
                    session()->put('cart.sum', $cartSum);
                    return true;
                }
            }
        }
        return false;
    }


    /*
     * Метод удаляет из сессию товар и меняем общее кол-во и общую сумму в корзине.
     * $product - объект товара.
     */
    public function destroy($product)
    {
        if ($product && session()->has('cart') && session()->has('cart.qty') && session()->has('cart.sum') && session()->has("cart.{$product->id}"))
        {
            // Получаем данные из корзины
            $cartQty = session()->get('cart.qty');
            $cartSum = session()->get('cart.sum');
            $productQty = session()->get("cart.{$product->id}.qty");
            $productPrice = session()->get("cart.{$product->id}.price");

            // Изменяем данные в корзине
            if (($cartQty - $productQty) <= 0) {

                // Если равно 0, то удаляем
                session()->forget('cart.qty');
            } else {
                session()->put('cart.qty', $cartQty - $productQty);
            }

            if (($cartSum - ($productQty * $productPrice)) <= 0) {

                // Если равно 0, то удаляем
                session()->forget('cart.sum');

            } else {
                session()->put('cart.sum', $cartSum - ($productQty * $productPrice));
            }

            // Удаляем из сессии товар
            session()->forget("cart.{$product->id}");
            return true;
        }
        return false;
    }
}
