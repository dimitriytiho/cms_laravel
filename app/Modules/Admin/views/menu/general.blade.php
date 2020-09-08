@extends("{$viewPath}.layouts.admin")
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
                    {!! $constructor::hidden('belong_id', $values->belong_id ?? $currentParentId) !!}
                    {!! $constructor::input('title', $values->title ?? null, null) !!}

                    <div class="d-flex justify-content-between w-100">
                        <div class="w-96">
                            {!! $constructor::input('slug', $values->slug ?? null) !!}
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-outline-primary btn-sm d-flex align-items-center mt-1 btn-pulse p-icons material-icons" id="slug-edit" title="@lang("{$lang}::a.generate_link")">autorenew</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">@lang("{$lang}::f.parent_id")</label>
                                {{--

                                Виджет меню--}}
                                @if (!empty($table))
                                    {!! Menu::init(
                                        [
                                            'tpl' => 'select_admin',
                                            'sql' => "SELECT id, parent_id, title FROM $table ORDER BY id DESC",
                                            'container' => 'select',
                                            'cache' => false,
                                            'class' => 'form-control custom-select',
                                            'attrs' => [
                                                'name' => 'parent_id',
                                            ],
                                            'before' => '<option value="0"> ' . __("{$lang}::f.parent_id") . ' </option>',
                                        ]
                                    ) !!}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            {!! $constructor::input('target', $values->target ?? null, null) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {!! $constructor::input('item', $values->item ?? null, null) !!}
                        </div>
                        <div class="col-md-6">
                            {!! $constructor::input('class', $values->class ?? null, null) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {!! $constructor::input('attr', $values->attr ?? null, null) !!}
                        </div>
                        @if (isset($values->id))
                            <div class="col-md-6">
                                {!! $constructor::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                            </div>
                            <div class="col-md-6">
                                {!! $constructor::input('sort', $values->sort ?? null, null) !!}
                            </div>
                        @endif
                    </div>

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

                    <div id="btn-sticky">
                        <button type="submit" class="btn btn-primary mt-3 btn-pulse">{{ isset($values->id) ? __("{$lang}::f.save") : __("{$lang}::f.submit") }}</button>
                    </div>
                </form>
                @if (isset($values->id))
                    <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm-form">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3  btn-pulse">@lang("{$lang}::s.remove")</button>
                    </form>
                @endif
            </div>
        </div>
    @else
        <h5>@lang("{$lang}::a.first_create_menu")</h5>
    @endif
    {!! config('admin.sticky_submit') ? $constructor::stickyScript() : null !!}
@endsection
