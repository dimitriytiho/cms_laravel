@php

    $cookie_locale = \Illuminate\Support\Facades\Cookie::get('locale');
    if ($cookie_locale) {
        app()->setLocale($cookie_locale);
    }

    $locale = app()->getLocale();
    $localeHelpers = !empty($namespaceHelpers) ? "{$namespaceHelpers}\\Locale" : null;
    $excludeCurrentLocale = $localeHelpers ? $localeHelpers::excludeCurrentLocale() : null;

    $path_segment = class_basename(Request::path());
    $create_edit = $path_segment === 'edit' || $path_segment === 'create';

    $table = $table ?? null;
    $class = $class ?? null;

@endphp
{{--

Основной шаблон по-умолчанию

--}}
<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="{{ asset(config('add.img') . '/omegakontur/admin/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/omegakontur/admin/touch-icon-ipad.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone-retina.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-ipad-retina.png') }}">
    {{-- <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,700|Material+Icons"> --}}
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Material+Icons">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css">
    <link rel="stylesheet" href="{{ asset('css/append.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    {{--

    Для файлового менеджера --}}
    @if ($path_segment === 'files')
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
    @endif

    {!! $getMeta !!}
    <noscript>
        <div class="container mt-4 mb-2">
            <div class="row">
                <div class="col">
                    <div class="alert alert-danger p-3">@lang("{$lang}::s.Please_enable_JavaScript")</div>
                </div>
            </div>
        </div>
    </noscript>
    {{--

    Подключаем менеджер для контента --}}
    @if ($create_edit)
        <link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.css" rel="stylesheet">
    @endif
</head>
<body>
<div class="app" id="app">
    @include('inc.header')
    <main class="container-fluid main">
        <div class="row">
            @include('inc.aside')

            <div class="col bg-light p-4 main-content">
                @include('inc.message')
                @include('inc.top_panel')
                <div class="col bg-white p-4 content">
                    <div class="py-4 px-1">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('inc.footer')
</div>
{{-- <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" defer></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous" defer></script> --}}
{{--

Вывод js кода из видов --}}
@stack('view_scripts')
{{--

    Для файлового менеджера --}}
@if ($path_segment === 'files')
    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endif
{{--

    Для страницы редактирования --}}
@if ($create_edit)
    {{--

    Выбор редактора кода --}}
    @if (config('admin.editor') === 'ckeditor')
        <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.config.height = '600px'
        </script>
        {{-- CKEDITOR.config.filebrowserImageBrowseUrl = '/file-manager/ckeditor'

        --}}
    @elseif(config('admin.editor') === 'codemirror')
        <script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var codemirror = document.querySelector('.codemirror')
                if (codemirror) {
                    editor = CodeMirror.fromTextArea(codemirror, {
                        tabMode: 'indent',
                        lineNumbers: true,
                        lineWrapping: true,
                        matchBrackets: true,
                        indentUnit: 4
                    })
                    editor.setSize('auto', 'auto')
                }
            }, false)

        </script>
    @endif
@endif
<script>
    var main = {
            url: '{{ route('admin.main') }}',
            cookie: {{ (int)config('admin.cookie') * 1000 }},
            asideWidthIcon: '{{ config("add.scss-admin.aside-width-icon") }}',
            asideWidthText: '{{ config("add.scss-admin.aside-width-text") }}'
        },
        {{--

        Dropzone --}}
        imgMaxSizeHD = {{ config('admin.imgMaxSizeHD') }},
        imgMaxSize = {{ config('admin.imgMaxSize') }},
        imgMaxSizeSM = {{ config('admin.imgMaxSizeSM') }},
        maxFilesOne = {{ config('admin.maxFilesOne') }},
        maxFilesMany = {{ config('admin.maxFilesMany') }},
        defaultImg = '{{ config("admin.img{$class}Default") }}',

        table = '{{ $table }}',
        currentClass = '{{ $class }}',

        imgRequestName = '{{ $imgRequestName ?? '' }}',
        imgUploadID = '{{ $imgUploadID ?? "" }}',
        curID = '{{ auth()->user()->id ?? "" }}'

    {!! \App\Helpers\Locale::translationsJson() !!}
        {{--asideWidth = {!! json_encode(config('admin.settings.aside_width')) !!}--}}
</script>
<script src="{{ asset('js/append.js') }}" defer></script>
</body>
</html>
