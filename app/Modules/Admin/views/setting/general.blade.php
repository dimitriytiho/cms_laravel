@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ isset($values->id) ? route("admin.{$route}.update", $values->id) : route("admin.{$route}.store") }}" method="post" class="needs-validation" novalidate>
                @if (isset($values->id))
                    @method('put')
                @endif
                @csrf

                {!! input('title', $values->title ?? null, null, 'text', null, null, null, [$disabled => null]) !!}
                @if ($disabled)
                        {!! hidden('title', $values->title ?? null) !!}
                @endif

                {!! input('value', $values->value ?? null, null) !!}

                @if (isset($values->id) && isset($values->updated_at) && isset($values->created_at))
                    <div class="row">
                        <div class="col-md-4">
                            {!! input('id', $values->id, null, 'text', true, null, null, ['disabled' => 'true']) !!}
                        </div>
                        <div class="col-md-4">
                            {!! input('updated_at', d($values->updated_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true']) !!}
                        </div>
                        <div class="col-md-4">
                            {!! input('created_at', d($values->created_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true'])!!}
                        </div>
                    </div>
                @endif

                <div>
                    <button type="submit" class="btn btn-primary mt-3 btn-pulse">{{ isset($values->id) ? __("{$lang}::f.save") : __("{$lang}::f.submit") }}</button>
                </div>
            </form>
            @if (isset($values->id))
                <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm-form">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3 btn-pulse">@lang("{$lang}::s.remove")</button>
                </form>
            @endif
        </div>
    </div>
@endsection
