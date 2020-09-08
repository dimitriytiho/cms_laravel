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
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.ip")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.qty")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.banned")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr @if ($v->banned == 1) class="table-active"@endif>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.show", $v->id) }}" class="font-weight-light">
                                        <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::a.edit")">visibility</i>
                                    </a>
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->ip }}</td>
                                <td class="font-weight-light">{{ $v->count }}</td>
                                <td class="font-weight-light">{{ $v->banned }}</td>
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
