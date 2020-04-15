@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if($values && !empty($menuName))
        @if(isset($menuName->title))
            <div class="row">
                <div class="col">
                    <p>
                        <span class="text-secondary">{{ __('a.selected') }}:&nbsp;</span>
                        <span>{{ $menuName->title }}</span>
                    </p>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col">
                <form action="{{ isset($values->id) ? route("admin.$route.update", $values->id) : route("admin.$route.store") }}" method="post" class="needs-validation" novalidate>
                    @if (isset($values->id))
                        @method('put')
                    @endif
                    @csrf
                    {!! hidden('menu_name_id', $values->menu_name_id ?? $current_menu_id) !!}
                    {!! input('title', $values->title ?? null, null) !!}

                    <div class="d-flex justify-content-between w-100">
                        <div class="w-96">
                            {!! input('slug', $values->slug ?? null) !!}
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-outline-primary btn-sm d-flex align-items-center mt-1 btn-pulse p-icons slug-edit" title="{{ __('a.generate_link') }}">
                                <i aria-hidden="true" class="material-icons slug-edit btn-pulse-child">autorenew</i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">{{ __('f.parent_id') }}</label>
                                @php

                                    if (!empty($table)) {
                                        new App\Widgets\Menu\Menu([
                                            'tpl' => MENU . '/select_admin',
                                            'sql' => "SELECT id, parent_id, title FROM $table WHERE menu_name_id = {$menuName->id} ORDER BY id DESC",
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
                        <div class="col-md-6">
                            {!! input('target', $values->target ?? null, null) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {!! input('item', $values->item ?? null, null) !!}
                        </div>
                        <div class="col-md-6">
                            {!! input('class', $values->class ?? null, null) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {!! input('attr', $values->attr ?? null, null) !!}
                        </div>
                        @if (isset($values->id))
                            <div class="col-md-6">
                                {!! input('sort', $values->sort ?? null, null) !!}
                            </div>
                        @endif
                    </div>

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

                    <div id="btn-sticky">
                        <button type="submit" class="btn btn-primary mt-3 btn-pulse">{{ isset($values->id) ? __('f.save') : __('f.submit') }}</button>
                    </div>
                </form>
                @if (isset($values->id))
                    <form action="{{ route("admin.$route.destroy", $values->id) }}" method="post" class="text-right confirm-form">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3  btn-pulse">{{ __('s.remove') }}</button>
                    </form>
                @endif
            </div>
        </div>
    @endif
    {!! stickyScript() !!}
@endsection
