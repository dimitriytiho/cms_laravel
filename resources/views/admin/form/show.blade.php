@extends('layouts.admin')
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
                            <th class="font-weight-light" scope="row">{{ __("f.id") }}</th>
                            <td>{{ $values->id }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.user_id") }}</th>
                            <td>{{ $values->user_id }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.name") }}</th>
                            <td>
                                <a href="{{ route("admin.user.edit", $values->user->id) }}">{{ $values->user->name }}</a>
                            </td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.email") }}</th>
                            <td>{{ $values->user->email }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.tel") }}</th>
                            <td>{{ $values->user->tel }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.message") }}</th>
                            <td>{{ $values->message }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.ip") }}</th>
                            <td>{{ $values->ip }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.created_at") }}</th>
                            <td class="text-secondary">{{ d($values->created_at, config('admin.date_format')) }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-light" scope="row">{{ __("f.updated_at") }}</th>
                            <td class="text-secondary">{{ d($values->updated_at, config('admin.date_format')) }}</td>
                        </tr>
                        {{--@foreach ($values as $k => $v)
                            @php

                                $v = $k === 'created_at' || $k === 'updated_at' ? d($v, config('admin.date_format')) : $v;

                            @endphp
                            <tr>
                                <th class="font-weight-light" scope="row">{{ __("f.$k") }}</th>
                                <td>{{ $v }}</td>
                            </tr>
                        @endforeach--}}
                        </tbody>
                    </table>
                    <form action="{{ route("admin.$route.destroy", $values->id) }}" method="post" class="text-right mb-5 confirm-form">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-outline-primary mt-3 btn-pulse">{{ __('s.remove') }}</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
