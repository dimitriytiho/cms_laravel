@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ isset($id) ? route("admin.{$route}.update", $id) : route("admin.{$route}.store") }}" method="post" class="needs-validation" novalidate>
                @if (isset($id))
                    @method('put')
                @endif
                @csrf

                {!! input('id', $id ?? null) !!}

                @if (!empty($locales))
                    @foreach ($locales as $locale)
                        {!! input($locale, $values[$locale] ?? null) !!}
                    @endforeach
                @endif

                <div>
                    <button type="submit" class="btn btn-primary mt-3 btn-pulse">{{ isset($id) ? __("{$lang}::f.save") : __("{$lang}::f.submit") }}</button>
                </div>
            </form>
            @if (isset($id))
                <form action="{{ route("admin.{$route}.destroy", $id) }}" method="post" class="text-right confirm-form">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3 btn-pulse">@lang("{$lang}::s.remove")</button>
                </form>
            @endif
        </div>
    </div>
@endsection
