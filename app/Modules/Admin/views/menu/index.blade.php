@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if ($menu)
        <div class="row mb-4">
            <div class="col">
                {!! select('current_menu', $menu, $current_menu_id, true, null, ['id' => 'select-change', 'data-action' => route("admin.$route.index")], null, true) !!}
            </div>
        </div>
    @endif
    @if (!empty($values))
        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">{{ __('a.action') }}</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">{{ __('a.title') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('a.slug') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr>
                                <th scope="row">
                                    <a href="{{ route("admin.$route.edit", $v->id) }}" class="font-weight-light">
                                        <i aria-hidden="true" class="material-icons" title="{{ __('a.edit') }}">edit</i>
                                    </a>
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->title }}</td>
                                <td class="font-weight-light">{{ $v->slug }}</td>
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
                <p class="font-weight-light text-center text-secondary mt-3">{{ __('a.shown') . $values->count() . __('a.of') .  $values->total()}}</p>
            </div>
        </div>
    @endif
@endsection
