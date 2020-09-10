@extends("{$viewPath}.layouts.admin")
{{--

Вывод контента

--}}
@section('content')
    @if ($values->isNotEmpty())
        <div class="row">
            <div class="col">
                @include("{$viewPath}.inc.search")
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::a.action")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.img")</th>
                            <th scope="col" class="font-weight-light">
                                <span>ID</span>
                                {!! $dbSort::viewIcons('id', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::a.title")</span>
                                {!! $dbSort::viewIcons('title', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::a.slug")</span>
                                {!! $dbSort::viewIcons('slug', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.status")</span>
                                {!! $dbSort::viewIcons('status', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::s.price")</span>
                                {!! $dbSort::viewIcons('price', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.sort")</span>
                                {!! $dbSort::viewIcons('sort', $view, $route) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr @if ($v->status === config('add.page_statuses')[0]) class="table-active"@endif>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.edit", $v->id) }}" class="font-weight-light">
                                        <i class="fas fa-eye" title="@lang("{$lang}::a.edit")"></i>
                                    </a>
                                </th>
                                <td>
                                    <img src="{{ asset($v->img) }}" class="w-3" alt="{{ $v->title }}">
                                </td>
                                <th scope="row" class="font-weight-light">{{ $v->id }}</th>
                                <td>{{ Lang::has("{$lang}::t.{$v->title}") ? __("{$lang}::t.{$v->title}") : $v->title }}</td>
                                <th class="font-weight-light">{{ $v->slug }}</th>
                                <th class="font-weight-light">@lang("{$lang}::s.{$v->status}")</th>
                                <th>{{ $v->price }}</th>
                                <td class="font-weight-light">{{ $v->sort }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col d-flex justify-content-center">
                <div>{{ $values->links() }}</div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p class="font-weight-light text-center text-secondary mt-3">{{ __("{$lang}::a.shown") . $values->count() . __("{$lang}::a.of") .  $values->total()}}</p>
            </div>
        </div>
    @endif
@endsection
