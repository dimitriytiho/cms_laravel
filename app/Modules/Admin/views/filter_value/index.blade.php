@extends("{$viewPath}.layouts.admin")
{{--

Вывод контента

--}}
@section('content')
    @if ($parentValues)
        <div class="row mb-4">
            <div class="col">
                {!! $constructor::select('current_group', $parentValues, $currentParentId, true, null, ['data-action' => route("admin.{$route}.index")], null, true, null, 'select-change') !!}
            </div>
        </div>
    @endif
    @if ($values && $values->isNotEmpty())
        <div class="row">
            <div class="col">
                @include("{$viewPath}.inc.search")
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::a.action")</th>
                            <th scope="col" class="font-weight-light">
                                <span>ID</span>
                                {!! $dbSort::viewIcons('id', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.title")</span>
                                {!! $dbSort::viewIcons('title', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.sort")</span>
                                {!! $dbSort::viewIcons('sort', $view, $route) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.edit", $v->id) }}" class="font-weight-light">
                                        <i class="fas fa-eye" title="@lang("{$lang}::a.edit")"></i>
                                    </a>
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ Lang::has("{$lang}::t.{$v->value}") ? __("{$lang}::t.{$v->value}") : $v->value }}</td>
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
