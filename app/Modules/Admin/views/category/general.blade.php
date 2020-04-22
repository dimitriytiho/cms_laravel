@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ isset($values->id) ? route("admin.$route.update", $values->id) : route("admin.$route.store") }}" method="post" class="needs-validation" novalidate>
                @if (isset($values->id))
                    @method('put')
                @endif
                @csrf
                {!! input('title', $values->title ?? null) !!}

                <div class="d-flex justify-content-between w-100">
                    <div class="w-96">
                        {!! input('slug', $values->slug ?? null) !!}
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center mt-1 p-0" title="{{ __('a.generate_link') }}">
                            <i aria-hidden="true" class="material-icons p-icons btn-pulse" id="slug-edit">autorenew</i>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {!! textarea('description', $values->description ?? null, null) !!}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="parent_id">{{ __('f.parent_id') }}</label>
                            @php

                                if (!empty($table)) {

                                    new App\Widgets\Menu\Menu([
                                        'tpl' => MENU . '/select_admin',
                                        'sql' => "SELECT id, parent_id, title FROM $table ORDER BY id DESC",
                                        'container' => 'select',
                                        'cache' => false,
                                        'class' => 'form-control custom-select',
                                        'attrs' => [
                                            'name' => 'parent_id',
                                        ],
                                        'prepend' => '<option value="0"> ' . __('f.parent_id') . ' </option>',
                                    ]);
                                }

                            @endphp
                        </div>
                    </div>
                </div>

                {!! textarea('body', $values->body ?? null, null, true, null, 'codemirror', null, 20) !!}
                @if (isset($values->id) && isset($values->updated_at) && isset($values->created_at))
                    <div class="row">
                        <div class="col-md-6">
                            {!! select('status', config('add.page_statuses'), $values->status ?? null) !!}
                        </div>
                        <div class="col-md-6">
                            {!! input('sort', $values->sort ?? null, null) !!}
                        </div>
                    </div>
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
                @else
                    <div class="row">
                        <div class="col">
                            {!! select('status', config('add.page_statuses'), $values->status ?? null) !!}
                        </div>
                    </div>
                @endif

                <div>
                    <span id="btn-sticky">
                        <button type="submit" class="btn btn-primary mt-3 mr-2 btn-pulse">{{ isset($values->id) ? __('f.save') : __('f.submit') }}</button>
                    </span>
                    @if (isset($values->slug))
                        <a href="{{ route($view, $values->slug) }}" class="btn btn-outline-primary mt-3 btn-pulse" target="_blank">{{ __('s.go') }}</a>
                    @endif
                </div>
            </form>
            @if (!empty($getIdParents) || !empty($getIdProducts))
                <div class="text-right mt--3">
                    <div class="small text-secondary">{{ __('s.remove_not_possible') }},<br>{{ __('s.there_are_nested') }} ID:</div>
                    @foreach ($getIdParents as $v)
                        <a href="{{ route("admin.$route.edit", $v->id) }}">{{ $v->id }}</a>
                    @endforeach

                    <div class="small text-secondary">{{ __('s.there_are_nested') }} {{ Str::lower(__('a.Products')) }}:</div>
                    @if(!empty($getIdProducts[0]->products))
                        @foreach ($getIdProducts[0]->products as $v)
                            <a href="{{ route("admin.product.edit", $v->id) }}">{{ $v->id }}</a>
                        @endforeach
                    @endif
                </div>
            @else
                @if (isset($values->id))
                    <form action="{{ route("admin.$route.destroy", $values->id) }}" method="post" class="text-right confirm-form">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3 btn-pulse">{{ __('s.remove') }}</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
    {!! $constructor::stickyScript() !!}
@endsection
