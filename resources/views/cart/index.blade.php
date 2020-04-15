@extends('layouts.default')
{{--

Подключается блок header

--}}
@section('header')
    @include('components.header')
@endsection

{{--

Вывод контента

--}}
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5">{{ $title }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col pt-4 no_js">
                    @include('cart.modal')
                    @if (!$cartSession)
                        <a href="{{ route('catalog') }}" class="btn btn-primary mt-4">{{ __('sh.catalog') }}</a>
                    @endif
                </div>
            </div>
            @if ($cartSession)
                <div class="row">
                    <div class="col-md-6 mt-3 mb-5">
                        <form method="post" action="{{ route('make_order') }}" class="needs-validation loader-submit" novalidate>
                        @csrf
                        {!! input('name', null, true, null, null) !!}
                        {!! input('tel', null, true, 'tel', null) !!}
                        {!! input('email', null, true, null, null) !!}
                        {!! textarea('address', null, true, null, 'address') !!}
                        {!! textarea('message', null, null, null, 'message') !!}
                        {!! checkbox('accept', null, true) !!}
                        <button type="submit" class="btn btn-primary">{{ __('f.submit') }}</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
{{--

Подключается блок footer

--}}
@section('footer')
    @include('components.footer')
@endsection
