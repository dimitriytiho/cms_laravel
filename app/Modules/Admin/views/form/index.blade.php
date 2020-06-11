@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if ($values->isNotEmpty())
        <div class="row">
            <div class="col">
                @include('inc.search')
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::a.action")</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.user_id")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.name")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.email")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.tel")</th>
                            <th scope="col" class="font-weight-light">IP</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr>
                                <th scope="row" class="d-flex align-items-center">
                                    <a href="{{ route("admin.{$route}.show", $v->id) }}" class="font-weight-light">
                                        <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::a.edit")">visibility</i>
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
