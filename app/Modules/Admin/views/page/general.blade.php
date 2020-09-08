@extends("{$viewPath}.layouts.admin")
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
                {{--


                Табы --}}
                @if (isset($values->id)){{-- На странице создания элемента табы не показываем  --}}
                <ul class="nav nav-tabs" role="tablist" id="tabs-edit">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-content" data-toggle="tab" href="#tab-content-link" role="tab" aria-controls="tab-content-link" aria-selected="true">@lang("{$lang}::a.content")</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-main" data-toggle="tab" href="#tab-main-link" role="tab" aria-controls="tab-main-link" aria-selected="false">@lang("{$lang}::a.main")</a>
                    </li>
                </ul>
                {{--

                Контент табов --}}
                <div class="tab-content" id="tabs-edit-content">
                    <div class="tab-pane fade show active pt-4" id="tab-content-link" role="tabpanel" aria-labelledby="tab-content">
                        {{--

                        Тело таба с контентом --}}
                        {!! $constructor::textarea('body', $values->body ?? null, null, true, null, config('admin.editor'), null, 20) !!}
                    </div>
                    <div class="tab-pane fade pt-4" id="tab-main-link" role="tabpanel" aria-labelledby="tab-main">
                @endif
                    {{--


                    Тело таба с основным --}}
                    {!! $constructor::input('title', $values->title ?? null) !!}

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
                            {!! $constructor::textarea('description', $values->description ?? null, null) !!}
                        </div>
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
                    </div>
                    {{-- На странице создания элемента табы не показываем  --}}
                @if (isset($values->id))
                    </div>
                </div>
                @endif
                {{-- Конец табов



                --}}
                @if (isset($values->id))
                    <div class="row">
                        <div class="col-md-6">
                            {!! $constructor::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                        </div>
                        <div class="col-md-6">
                            {!! $constructor::input('sort', $values->sort ?? null, null) !!}
                        </div>
                    </div>
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
                @else
                    <div class="row">
                        <div class="col">
                            {!! $constructor::select('status', config('add.page_statuses'), $values->status ?? null) !!}
                        </div>
                    </div>
                @endif
                <div>
                    <span id="btn-sticky">
                        <button type="submit" class="btn btn-primary mt-3 mr-2 btn-pulse">{{ isset($values->id) ? __("{$lang}::f.save") : __("{$lang}::f.submit") }}</button>
                    </span>
                    @if (isset($values->slug))
                        <a href="{{ route($view, $values->slug) }}" class="btn btn-outline-primary mt-3 btn-pulse" target="_blank">@lang("{$lang}::s.go")</a>
                    @endif
                </div>
            </form>
            @if (!empty($getIdParents))
                <div class="text-right mt--3">
                    <div class="small text-secondary">@lang("{$lang}::s.remove_not_possible"),<br>@lang("{$lang}::s.there_are_nested") ID:</div>
                    @foreach ($getIdParents as $v)
                        <a href="{{ route("admin.{$route}.edit", $v->id) }}">{{ $v->id }}</a>
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
    {!! config('admin.sticky_submit') ? $constructor::stickyScript() : null !!}
@endsection
