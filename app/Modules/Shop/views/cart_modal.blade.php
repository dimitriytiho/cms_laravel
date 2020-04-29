@if (!empty($cartSession))
    <div class="table-responsive cart_block">
        <table class="table">
            <thead>
            <tr>
                <th class="font-weight-light" scope="col">@lang("{$lang}::sh.id")</th>
                <th class="font-weight-light" scope="col">@lang("{$lang}::sh.image")</th>
                <th class="font-weight-light" scope="col">@lang("{$lang}::sh.title")</th>
                <th class="font-weight-light" scope="col">@lang("{$lang}::sh.quantity")</th>
                <th class="font-weight-light" scope="col">@lang("{$lang}::sh.price")</th>
                <th class="font-weight-light" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($cartSession as $cartID => $cartProduct)
                @if (is_array($cartProduct))
                    <tr>
                        <th class="font-weight-light" scope="row">{{ $cartID }}</th>
                        <td>
                            <a href="{{ route('product', $cartProduct['slug']) }}">
                                <img src="{{ asset($cartProduct['img']) }}" class="w-5" alt="{{ $cartProduct['title'] }}">
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('product', $cartProduct['slug']) }}">{{ $cartProduct['title'] }}</a>
                        </td>
                        <td>
                            <a href="{{ route('cart_minus', $cartID) }}" class="btn cart_minus" data-id="{{ $cartID }}">
                                <span>@lang("{$lang}::sh.minus")</span>
                            </a>
                            <span class="cart_modal_product_qty">{{ $cartProduct['qty'] }}</span>
                            <a href="{{ route('cart_plus', $cartID) }}" class="btn cart_plus" data-id="{{ $cartID }}">
                                <span>@lang("{$lang}::sh.plus")</span>
                            </a>
                        </td>
                        <td>{{ $cartProduct['price'] }}</td>
                        <td>
                            <a href="{{ route('cart_destroy', $cartID) }}" aria-label="@lang("{$lang}::s.Close")" class="cart_destroy" data-id="{{ $cartID }}" aria-hidden="true">&times;</a>
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th colspan="4">@lang("{$lang}::sh.total"):</th>
                <th id="cart_modal_qty">{{ $cartSession['qty'] }}</th>
                <th></th>
            </tr>
            <tr>
                <th colspan="4">@lang("{$lang}::sh.sum"):</th>
                <th id="cart_modal_sum">{{ $cartSession['sum'] }}</th>
                <th></th>
            </tr>
            </tbody>
        </table>
    </div>
    @if (!isset($noBtnModal))
        {!! modalFooter() !!}
    @endif
@else
    <h4 class="font-weight-light my-3">@lang("{$lang}::sh.cart_empty")</h4>
    @if (!isset($noBtnModal))
        {!! modalFooter(false) !!}
    @endif
@endif
