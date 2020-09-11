@php

    use App\Modules\Admin\Helpers\Img;


    $cookie_locale = Cookie::get('locale');
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
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.13/css/all.css">
    {{--<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,700|Material+Icons">--}}
    {{--<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Material+Icons">--}}
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css">
    <link rel="stylesheet" href="{{ asset('css/append.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    {{--

    Для файлового менеджера --}}
    @if ($path_segment === 'files')
        {{--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">--}}
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
<div class="content-block">
    <div class="app" id="app">
        @include("{$viewPath}.inc.aside")
        <div class="px-0 w-100 main-content">
            @include("{$viewPath}.inc.header")
            <div class="row body-block mr-2 ml-3">
                <div class="col transition {{--aside-margin-change--}}" {{--style="margin-left: {{ $asideWidth }};"--}}>
                    @include("{$viewPath}.inc.message")
                    @include("{$viewPath}.inc.top_panel")
                    <div class="row" id="content">
                        <div class="col mt-1 content">
                            <div class="py-4 px-1">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("{$viewPath}.inc.footer")
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
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
                    editor.setSize('auto', '704')
                    //editor.setSize('auto', 'auto')
                }
            }, false)

        </script>
    @endif
@endif
<script>
    var main = {
            siteName: '{{ config('add.name') ?: 'Site' }}',
            url: '{{ route('admin.main') }}',
            cookie: {{ (int)config('admin.cookie') * 1000 }}
            {{--asideWidthIcon: '{{ config("admin.scss.aside-width-icon") }}',
            asideWidthText: '{{ config("admin.scss.aside-width-text") }}'--}}
        },
        {{--

        Dropzone --}}
        acceptedImagesExt = '{{ Img::acceptedImagesExt() }}',
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

    {!! HelpersLocale::translationsJson() !!}
        {{--asideWidth = {!! json_encode(config('admin.settings.aside_width')) !!}--}}
</script>
<script src="{{ asset('js/append.js') }}" defer></script>
</body>
</html>
