@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if (!empty($values))
        <div class="row">
            <div class="col">
                @include('inc.search')
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::a.action")</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.title")</th>
                            {{--<th scope="col" class="font-weight-light">@lang("{$lang}::f.type")</th>--}}
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.value")</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.section")</th>
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
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->title }}</td>
                                {{--<td>{{ $v->type }}</td>--}}
                                <td class="font-weight-light">{{ Str::limit($v->value, 20) }}</td>
                                <td class="font-weight-light">{{ $v->section }}</td>
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
        <div class="row">
            <div class="col">
                <div class="border text-secondary rounded my-4 p-4">
                    <span class="font-weight-light">@lang("{$lang}::a.example_use_in_views")</span>
                    <span>@{{ \App\Main::site('name') }}</span>
                </div>
            </div>
        </div>
    @endif
@endsection
