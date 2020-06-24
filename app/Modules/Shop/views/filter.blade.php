@foreach ($products as $product)
    <div class="col-md-4 mb-4">
        <div class="card">
            <a href="{{ route('product', $product->slug) }}">
                <img src="{{ asset($product->img) }}" class="card-img-top" alt="{{ $product->title }}">
            </a>
            <div class="card-body">
                <h5 class="card-title">
                    <a href="{{ route('product', $product->slug) }}">
                        {{ $product->title }}
                    </a>
                </h5>
                <p class="card-text">Some quick example text to build on the card title.</p>
                <a href="{{ route('cart_plus', $product->id) }}" class="btn btn-outline-dark btn-sm cart_plus" data-id="{{ $product->id }}">@lang("{$lang}::s.add_to_cart")</a>
            </div>
        </div>
    </div>
@endforeach
{{--

Пагинация--}}
<div class="col-12 d-flex justify-content-center mt-4">
    <div>{{ $products->links() }}</div>
</div>
<div class="col-12">
    <p class="font-weight-light text-center text-secondary mt-3">{{ __("{$lang}::a.shown") . $products->count() . __("{$lang}::a.of") .  $products->total()}}</p>
</div>
