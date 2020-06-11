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
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.img")</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.name")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.email")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.tel")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.role_id")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.role")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.ip")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.edit", $v->id) }}" class="font-weight-light">
                                        <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::a.edit")">visibility</i>
                                    </a>
                                </th>
                                <td>
                                    <img src="{{ asset($v->img) }}" class="w-3" alt="{{ $v->title }}">
                                </td>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td class="no-wrap">{{ $v->name }}</td>
                                <td class="font-weight-light">{{ $v->email }}</td>
                                <td class="font-weight-light">{{ $v->tel }}</td>
                                <td class="font-weight-light">{{ $v->role->id }}</td>
                                <td>{{ __("{$lang}::s.{$v->role->name}") }}</td>
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
