@php

use App\App;
use App\Widgets\PanelDashboard\PanelDashboard;
use App\Helpers\Locale;

@endphp
{{--

Основной шаблон по-умолчанию

--}}
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset(config('add.img') . '/logo/touch-icon-iphone.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset(config('add.img') . '/logo/touch-icon-ipad.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset(config('add.img') . '/logo/touch-icon-iphone-retina.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset(config('add.img') . '/logo/touch-icon-ipad-retina.png') }}">
    {{-- <link href="//fonts.googleapis.com/css?family=Roboto:300,400,700&amp;subset=cyrillic" rel="stylesheet"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {!! $getMeta !!}
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    @include("{$viewPath}.inc.warning")
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    {{--

    Здесь можно добавить файлы css --}}
    @yield('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/add.css') }}">
</head>
<body>
{{--

    Панель администратора --}}
{!! PanelDashboard::run() !!}

<div id="app">
    @yield('header')
    @include("{$viewPath}.inc.message")

    <div class="content" id="content">
        @yield('content')
    </div>
    <div id="bottom-block"></div>

    @yield('footer')
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
{{--

@if (!request()->is('/'))
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous" defer></script>
@endif
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous" defer></script>

--}}
<script>
    var body = document.querySelector('body'),
        path = '{{ route('index') }}',
        slug = '{{ str_replace('-', '_', request()->path()) }}',
        site_title = '{{ App::get('settings')['site_name'] ?? ' ' }}',
        site_tel = '{{ App::get('settings')['tel'] ?? ' ' }}',
        site_email = '{{ App::get('settings')['site_email'] ?? ' ' }}',
        main_color = '{{ config('add.scss')['primary'] ?: '#ccc' }}',
        height = '{{ config('add.height') ?: 600 }}'

    {!! Locale::translationsJson() !!}
</script>
{{--

Если в контенте есть скрипты, то они выведятся здесь, через метод App::getDownScript() --}}
@if (!empty(App::get('scripts')))
    @foreach (App::get('scripts') as $script)
        {!! $script . PHP_EOL !!}
    @endforeach
@endif
<script src="{{ asset('js/app.js') }}" defer></script>
{{--

Здесь можно добавить файлы js --}}
@yield('js')
<script src="{{ asset('js/add.js') }}" defer></script>
{{--

Вывод js кода из вида pages.contact_us --}}
@stack('novalidate')
</body>
</html>
