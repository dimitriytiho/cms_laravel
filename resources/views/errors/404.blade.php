@extends('layouts.default')

{{-- Подключается блок header --}}
@section('header')
    @include('components.header')
@endsection

{{-- Вывод контента --}}
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5 mb-4">{{ $title }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="mt-3 mb-5">{{ $message . __('s.you_can_go') }}</p>
                    <div>
                        <a href="javascript:history.back()" class="btn btn-outline-dark"><i class="fa fa-arrow-left"></i> Назад</a>
                        <a href="{{ route('main') }}" class="btn btn-primary"><i class="fa fa-home"></i> Главная</a>
                    </div>
                </div>
                <div class="col-md-6 text-md-center">
                    <picture>
                        <source srcset="{{ asset(config('add.img') . '/error/404.svg') }}" type="image/svg+xml">
                        <img src="{{ asset(config('add.img') . '/error/404.jpg') }}" class="img-fluid w-50" alt="{{ $title }}">
                    </picture>
                </div>
            </div>
        </div>
    </main>
@endsection

{{-- Подключается блок footer --}}
@section('footer')
    @include('components.footer')
@endsection
