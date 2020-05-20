@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    {{-- <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="main-tab" data-toggle="tab" href="#main" role="tab" aria-controls="main" aria-selected="true">@lang("{$lang}::a.main")</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="content-tab" data-toggle="tab" href="#content" role="tab" aria-controls="content" aria-selected="false">@lang("{$lang}::a.content")</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active pt-4" id="main" role="tabpanel" aria-labelledby="main-tab">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa, quo1!
        </div>
        <div class="tab-pane fade pt-4" id="content" role="tabpanel" aria-labelledby="content-tab">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa, quo2!
        </div>
    </div> --}}
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
                        <li class="nav-item">
                            <a class="nav-link" id="tab-gallery" data-toggle="tab" href="#tab-gallery-link" role="tab" aria-controls="tab-gallery-link" aria-selected="false">@lang("{$lang}::a.gallery")</a>
                        </li>
                    </ul>
                    {{--

                    Контент табов --}}
                    <div class="tab-content" id="tabs-edit-content">
                        <div class="tab-pane fade show active pt-4" id="tab-content-link" role="tabpanel" aria-labelledby="tab-content">
                            {{--


                            Тело таба с контентом --}}
                            {!! textarea('body', $values->body ?? null, null, true, null, 'codemirror', null, 20) !!}
                        </div>
                        <div class="tab-pane fade pt-4" id="tab-main-link" role="tabpanel" aria-labelledby="tab-main">
                @endif
                        {{--


                        Тело таба с основным --}}
                        {!! input('title', $values->title ?? null) !!}

                        <div class="d-flex justify-content-between w-100">
                            <div class="w-96">
                                {!! input('slug', $values->slug ?? null) !!}
                            </div>
                            <div class="mt-4">
                                <button class="btn btn-outline-primary btn-sm d-flex align-items-center mt-1 btn-pulse p-icons material-icons" id="slug-edit" title="@lang("{$lang}::a.generate_link")">autorenew</button>
                            </div>
                        </div>

                        {!! textarea('description', $values->description ?? null, null) !!}

                        <div class="row">
                            <div class="col-md-6">{{-- pattern="^[0-9.]{1,}$" --}}
                                {!! input('old_price', $values->old_price ?? null, null, null, true, null, null, ['pattern' => '^[0-9.]{1,}$']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! input('price', $values->price ?? null, true, null, true, null, null, ['pattern' => '^[0-9.]{1,}$']) !!}
                            </div>
                        </div>

                        @if (isset($values->id))
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_id">@lang("{$lang}::f.category_id")</label>
                                        {{--

                                        Виджет меню--}}
                                        {!! Menu::init(
                                            [
                                                'tpl' => 'select_admin_belongs',
                                                'sql' => "SELECT id, parent_id, title FROM categories ORDER BY id DESC",
                                                'container' => 'select',
                                                'cache' => false,
                                                'class' => 'form-control custom-select',
                                                'attrs' => [
                                                    'name' => 'category_id',
                                                    'id' => 'select-product-category',
                                                ],
                                                'prepend' => '<option value="0"> ' . __("{$lang}::s.choose") . ' </option>',
                                            ]
                                        ) !!}
                                    </div>
                                    <button data-url="{{ route('admin.product_add_category') }}" data-id="{{ $values->id }}" data-url-destroy="{{ route('admin.product_destroy_category') }}" data-belongs-id data-belongs-title data-belongs-title-lang class="btn btn-outline-primary btn-pulse js-none" id="product-add-category">@lang("{$lang}::s.choose")</button>
                                </div>
                                <div class="col-md-6 mt-1">
                                    <div class="border mt-4 px-2 py-1" id="category-many-elements">
                                        @foreach ($values->category as $v)
                                            <div class="mr-4 many-elements">
                                                <span class="many-elements__text">{{ $v->title }}</span>
                                                <a data-url="{{ route('admin.product_destroy_category') }}" data-belongs-id="{{ $v->id }}" data-id="{{ $values->id }}" class="text-primary many-elements__close cur">&times;</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @if ($filters)
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="filter_value">@lang("{$lang}::a.Filter_value")</label>
                                            <select class="form-control custom-select" name="filter_value" id="select-product-filter">
                                                <option value="0"> Выбрать </option>
                                                @foreach ($filters as $v)
                                                    @php

                                                      $filterLang = Lang::has("{$lang}::t.{$v->value}") ? __("{$lang}::t.{$v->value}") : $v->value;
                                                      $filterGroup = Lang::has("{$lang}::t.{$filterGroups[$v->parent_id]->title}") ? __("{$lang}::t.{$filterGroups[$v->parent_id]->title}") : $filterGroups[$v->parent_id]->title;

                                                    @endphp
                                                    <option data-title="{{ $v->value }}" data-title-lang="{{ $filterLang }}" value="{{ $v->id }}" @if ($filtersActive->filter_values->contains('value', $v->value))disabled @endif>{{ $filterGroup }} - {{ $filterLang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button data-url="{{ route('admin.product_add_filter') }}" data-id="{{ $values->id }}" data-url-destroy="{{ route('admin.product_destroy_filter') }}" data-belongs-id data-belongs-title data-belongs-title-lang class="btn btn-outline-primary btn-pulse js-none" id="product-add-filter">@lang("{$lang}::s.choose")</button>
                                    </div>
                                    <div class="col-md-6 mt-1">
                                        <div class="border mt-4 px-2 py-1" id="filter-many-elements">
                                            @foreach ($filtersActive->filter_values as $v)
                                                <div class="mr-4 many-elements">
                                                    <span class="many-elements__text">{{ Lang::has("{$lang}::t.{$v->value}") ? __("{$lang}::t.{$v->value}") : $v->value }}</span>
                                                    <a data-url="{{ route('admin.product_destroy_filter') }}" data-belongs-id="{{ $v->id }}" data-id="{{ $values->id }}" class="text-primary many-elements__close cur">&times;</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        {{-- На странице создания элемента табы не показываем  --}}
                @if (isset($values->id))
                        </div>
                        <div class="tab-pane fade show active pt-4" id="tab-gallery-link" role="tabpanel" aria-labelledby="tab-gallery">
                            {{--


                            Тело таба галереи картинок --}}
                            {!! hidden('img', $values->img) !!}
                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-center align-items-center img-view" id="dropzone-images">
                                    <a href="{{ asset($values->img) }}" class="ml-3 mt-3" target="_blank">
                                        @if ($values->img !== config("admin.img{$class}Default"))
                                            <i class="material-icons img-remove" data-img="{{ $values->img }}" data-max-files="{{ config('admin.maxFilesOne') }}">clear</i>
                                        @endif
                                        <img src="{{ asset($values->img) }}" alt="@lang("{$lang}::f.img")">
                                    </a>
                                </div>
                                {{--

                                Dropzone one --}}
                                <div class="col-md-6">
                                    <div id="dzOne" class="dropzone"></div>
                                </div>
                            </div>
                            <div class="row mt-5 mb-3">
                                {{--

                                Dropzone gallery --}}
                                <div class="col-12">
                                    <p class="label mb-2">@lang("{$lang}::a.image_gallery")</p>
                                    <div id="dzMany" class="dropzone"></div>
                                </div>
                                <div class="col-12 mt-4 img-view" id="dropzone-gallery">
                                    @if (!empty($gallery))
                                        @foreach ($gallery as $img)
                                            <a href="{{ asset($img->img) }}" target="_blank">
                                                <i class="material-icons img-remove" data-img="{{ $img->img }}" data-max-files="{{ config('admin.maxFilesMany') }}">clear</i>
                                                <img src="{{ asset($img->img) }}" alt="@lang("{$lang}::f.img")">
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Конец табов



                --}}
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
