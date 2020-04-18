{{--

Подключаем css файл --}}
@if(is_file(public_path("css/{$m}.css")))
    @section('css')
        <link rel="stylesheet" type="text/css" href="{{ asset("css/{$m}.css") }}">
    @endsection
@endif
{{--

Наследуем шаблон --}}
@extends("{$viewPath}.default")
{{--

Подключается блок header --}}
@section('header')
    @include("{$viewPath}.inc.header")
@endsection
{{--


Вывод контента

--}}
@section('content')
    @if (!empty($values))
        <main class="main">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h1 class="font-weight-light text-secondary mt-5">{{ $values->title }}</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col my-4">
                        {!! $values->body !!}
                    </div>
                </div>
            </div>
        </main>
    @endif
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include("{$viewPath}.inc.footer")
@endsection
{{--

Подключаем js файл --}}
@if(is_file(public_path("js/{$m}.js")))
    @section('js')
        <script src="{{ asset("js/{$m}.js") }}" defer></script>
    @endsection
@endif
