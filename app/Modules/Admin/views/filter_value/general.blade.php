@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if ($values && $parentValues)
        @if (isset($parentValues->title))
            <div class="row">
                <div class="col">
                    <p>
                        <span class="text-secondary">@lang("{$lang}::a.selected"):&nbsp;</span>
                        <span>{{ $parentValues->title }}</span>
                    </p>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col">
                <form action="{{ isset($values->id) ? route("admin.{$route}.update", $values->id) : route("admin.{$route}.store") }}" method="post" class="needs-validation" novalidate>
                    @if (isset($values->id))
                        @method('put')
                    @endif
                    @csrf
                    {!! $constructor::hidden('parent_id', $values->parent_id ?? $currentParentId) !!}
                    {!! $constructor::input('value', $values->value ?? null) !!}
                    
                    @if (isset($values->id))
                        {!! $constructor::input('sort', $values->sort ?? null, null) !!}
                    @endif

                    @if (isset($values->id) && isset($values->updated_at) && isset($values->created_at))
                        <div class="row">
                            <div class="col-md-4">
                                {!! $constructor::input('id', $values->id, null, 'text', true, null, null, ['disabled' => 'true']) !!}
                            </div>
                            <div class="col-md-4">
                                {!! $constructor::input('updated_at', d($values->updated_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true']) !!}
                            </div>
                            <div class="col-md-4">
                                {!! $constructor::input('created_at', d($values->created_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true'])!!}
                            </div>
                        </div>
                    @endif

                    <div>
                        <button type="submit" class="btn btn-primary mt-3 btn-pulse">{{ isset($values->id) ? __("{$lang}::f.save") : __("{$lang}::f.submit") }}</button>
                    </div>
                </form>
                @if (!empty($getIdParents))
                    <div class="text-right mt--3">
                        <div class="small text-secondary">@lang("{$lang}::s.remove_not_possible"),<br>@lang("{$lang}::s.there_are_parents") ID:</div>
                        @foreach ($getIdParents as $v)
                            <a href="{{ route("admin.{$belongsRoute}.edit", $v->id) }}">{{ $v->id }}</a>
                        @endforeach
                    </div>
                @else
                    @if (isset($values->id))
                        <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm-form">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3 btn-pulse">@lang("{$lang}::s.remove")</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    @endif
@endsection
