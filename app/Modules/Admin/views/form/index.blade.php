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
                            <th scope="col" class="font-weight-light">
                                <span>ID</span>
                                {!! $dbSort::viewIcons('id', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.user_id")</span>
                                {!! $dbSort::viewIcons('user_id', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.name")</span>
                                {!! $dbSort::viewIcons('name', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.email")</span>
                                {!! $dbSort::viewIcons('email', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.tel")</span>
                                {!! $dbSort::viewIcons('tel', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.ip")</span>
                                {!! $dbSort::viewIcons('ip', $view, $route) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr>
                                <th scope="row" class="d-flex align-items-center">
                                    <a href="{{ route("admin.{$route}.show", $v->id) }}" class="font-weight-light">
                                        <i class="fas fa-eye" title="@lang("{$lang}::a.edit")"></i>
                                    </a>
                                    {{--<form action="{{ route("admin.{$route}.destroy", $v->id) }}" method="post" class="confirm-form">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-pulse"><i aria-hidden="true" class="material-icons" title="@lang("{$lang}::s.remove")">delete_outline</i></button>
                                    </form>--}}
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->user->id }}</td>
                                <td>
                                    <a href="{{ route("admin.user.edit", $v->user->id) }}">{{ $v->user->name }}</a>
                                </td>
                                <td class="font-weight-light">{{ $v->user->email }}</td>
                                <td>{{ $v->user->tel }}</td>
                                <td class="font-weight-light">{{ $v->ip }}</td>
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
