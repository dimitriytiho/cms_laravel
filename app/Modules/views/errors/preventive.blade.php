@extends("{$viewPath}.default")
{{--

Подключается блок header --}}
@section('header')
    @include("{$viewPath}.inc.header")
@endsection

{{-- Вывод контента --}}
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5 mb-4">@lang('s.Preventive_work')</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="mt-3 mb-5">@lang('s.Preventive_work_go')</p>
                    <p class="mt-3 mb-5">{!! __('s.Preventive_work_contact', ['email' => \App\App::get('settings')['site_email'] ?? ' ', 'tel' => \App\App::get('settings')['tel'] ?? ' ']) !!}.</p>
                </div>
                <div class="col-md-6 text-md-center">
                    <picture>
                        <source srcset="{{ config('add.img') . '/error/error.svg' }}" type="image/svg+xml">
                        <img src="{{ config('add.img') . '/error/error.jpg' }}" class="img-fluid w-50" alt="@lang('s.Preventive_work')">
                    </picture>
                </div>
            </div>
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include("{$viewPath}.inc.footer")
@endsection
