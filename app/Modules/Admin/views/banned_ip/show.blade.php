@extends("{$viewPath}.layouts.admin")
{{--

Вывод контента --}}
@section('content')
    @if (!empty($values))
        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th class="font-weight-light" scope="row">@lang("{$lang}::f.id")</th>
                            <td>{{ $values->id }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">@lang("{$lang}::f.ip")</th>
                            <td>{{ $values->ip }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">@lang("{$lang}::f.qty")</th>
                            <td>{{ $values->count }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">@lang("{$lang}::f.banned")</th>
                            <td>{{ $values->banned }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">@lang("{$lang}::f.created_at")</th>
                            <td class="text-secondary">{{ d($values->created_at, config('admin.date_format')) }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">@lang("{$lang}::f.updated_at")</th>
                            <td class="text-secondary">{{ d($values->updated_at, config('admin.date_format')) }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right mb-5 confirm-form">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-outline-primary mt-3 btn-pulse">@lang("{$lang}::s.clear")</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
