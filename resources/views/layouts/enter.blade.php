{{--

Основной страницы входа в админку

--}}
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('touch-icon-iphone.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('touch-icon-ipad.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('touch-icon-iphone-retina.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('touch-icon-ipad-retina.png') }}">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Material+Icons">
    <link rel="stylesheet" href="{{ asset('css/append.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! $getMeta !!}
    <noscript>
        <div class="container mt-4 mb-2">
            <div class="row">
                <div class="col">
                    <div class="alert alert-danger p-3">{{ __('service.Please_enable_JavaScript') }}</div>
                </div>
            </div>
        </div>
    </noscript>
</head>
<body class="bg-light">
<div class="app" id="app">
    <div class="container-fluid">
        <div class="mt-2 a-primary">
            <a href="{{ route('main') }}" title="{{ __('c.home') }}">
                <i class="material-icons">apps</i>
            </a>
        </div>
    </div>
    <div class="container">
        @include('components.message')
    </div>
    <div class="content" id="content">
        @yield('content')
    </div>
    <div id="bottom-block"></div>
</div>
<script type="text/javascript">
    var body = document.body,
        height = '{{ config('add.height') ?: 600 }}',
        main = {
            url: '{{ env('APP_URL') . '/' }}'
    }

    {!! \App\Helpers\Locale::translationsJson() !!}
</script>
<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
</body>
</html>
