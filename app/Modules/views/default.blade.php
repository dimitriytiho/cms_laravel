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
    {{--

    Здесь можно добавить файлы css --}}
    @yield('css')
    {{--

    Объединяем css в один файл --}}
    {{--{{ HelpersFile::merge(
        [
            'css/app.css',
            'css/add.css',
        ],
        'css/main.css'
    ) }}
     <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/add.css') }}">
</head>
<body>
{{--

    Панель администратора --}}
{!! PanelDashboard::init() !!}
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
    var body = $('body'),
        _token = document.querySelector('meta[name="csrf-token"]'),
        path = '{{ route('index') }}',
        slug = '{{ str_replace('-', '_', request()->path()) }}',
        site_title = '{{ Main::site('name') ?: ' ' }}',
        site_tel = '{{ Main::site('tel') ?: ' ' }}',
        site_email = '{{ Main::site('email') ?: ' ' }}',
        main_color = '{{ config('add.scss')['primary'] ?? '#ccc' }}',
        height = '{{ config('add.height') ?? 600 }}'

    if (_token) {
        _token = _token.content
    }

    {!! HelpersLocale::translationsJson() !!}
</script>
{{--

Если в контенте есть скрипты, то они выведятся здесь, через метод Main::getDownScript() --}}
@if (!empty(Main::get('scripts')))
    @foreach (Main::get('scripts') as $script)
        {!! $script . PHP_EOL !!}
    @endforeach
@endif
{{--

Здесь можно добавить файлы js --}}
@yield('js')
{{--

Вывод js кода из вида pages.contact_us --}}
@stack('novalidate')
{{--

Объединяем скрипты в один файл --}}
{{--{{ HelpersFile::merge(
    [
        'js/app.js',
        'js/add.js',
    ],
    'js/main.js'
) }}
<script src="{{ asset('js/main.js') }}" defer></script>--}}
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/add.js') }}" defer></script>
</body>
</html>
