@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if (!empty($values))
        <div class="row">
            <div class="col">
                <form action="{{ route("admin.{$route}.index") }}" class="mb-3">
                    <div class="form-row">
                        <div class="col-sm-2 mb-2">
                            <label for="col" class="sr-only"></label>
                            <select class="form-control" name="col">
                                @if ($queryArr)
                                    @foreach ($queryArr as $option)
                                        <option value="{{ $option }}" @if ($col === $option) selected @endif>@lang("{$lang}::f.{$option}")</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col col-sm-3">
                            <label for="cell" class="sr-only"></label>
                            <input type="text" name="cell" class="form-control" placeholder="@lang("{$lang}::a.search")..." value="@if ($cell){{ $cell }}@endif">
                        </div>
                        <div class="col-1 d-flex">
                            <div>
                                <button type="submit" class="btn btn-primary btn-icons">
                                    <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::a.search")">search</i>
                                </button>
                            </div>
                            @if ($cell)
                                <div>
                                    <a href="{{ route("admin.{$route}.index") }}" class="btn btn-outline-primary ml-2 btn-icons">
                                        <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::c.reset")">find_replace</i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::a.action")</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.title")</th>
                            {{--<th scope="col" class="font-weight-light">@lang("{$lang}::f.type")</th>--}}
                            <th scope="col" class="font-weight-light">@lang("{$lang}::f.value")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.edit", $v->id) }}" class="font-weight-light">
                                        <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::a.edit")">edit</i>
                                    </a>
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->title }}</td>
                                {{--<td>{{ $v->type }}</td>--}}
                                <td class="font-weight-light">{{ $v->value }}</td>
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
                    <span>@{{ \App\App::get('settings')['site_name'] ?? null }}</span>
                </div>
            </div>
        </div>
    @endif
@endsection
