@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    @if (!empty($values))
        <div class="row">
            <div class="col">
                <form action="{{ route("admin.$route.index") }}" class="mb-3">
                    <div class="form-row">
                        <div class="col-sm-2 mb-2">
                            <label for="col" class="sr-only"></label>
                            <select class="form-control" name="col">
                                @if ($queryArr)
                                    @foreach ($queryArr as $option)
                                        <option value="{{ $option }}" @if ($col === $option) selected @endif>{{ __("f.{$option}") }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col col-sm-3">
                            <label for="cell" class="sr-only"></label>
                            <input type="text" name="cell" class="form-control" placeholder="{{ __('a.search') }}..." value="@if ($cell){{ $cell }}@endif">
                        </div>
                        <div class="col-1 d-flex">
                            <div>
                                <button type="submit" class="btn btn-primary btn-icons">
                                    <i aria-hidden="true" class="material-icons" title="{{ __('a.search') }}">search</i>
                                </button>
                            </div>
                            @if ($cell)
                                <div>
                                    <a href="{{ route("admin.$route.index") }}" class="btn btn-outline-primary ml-2 btn-icons">
                                        <i aria-hidden="true" class="material-icons" title="{{ __('c.reset') }}">find_replace</i>
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
                            <th scope="col" class="font-weight-light">{{ __('a.action') }}</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">{{ __('f.user_id') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('f.name') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('f.email') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('f.tel') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('s.qty') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('s.sum') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('f.status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            @php

                            if ($v->status === config('admin.order_statuses')[0]) {
                                $trClass = 'table-danger';

                            /*} elseif () {*/

                            } else {

                                $trClass = null;
                            }

                            @endphp
                            <tr class="{{ $trClass }}">
                                <th scope="row" class="d-flex align-items-center">
                                    <a href="{{ route("admin.$route.show", $v->id) }}" class="font-weight-light">
                                        <i aria-hidden="true" class="material-icons" title="{{ __('a.edit') }}">edit</i>
                                    </a>
                                    {{--<form action="{{ route("admin.$route.destroy", $v->id) }}" method="post" class="confirm-form">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-pulse"><i aria-hidden="true" class="material-icons" title="{{ __('s.remove') }}">delete_outline</i></button>
                                    </form>--}}
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->user->id }}</td>
                                <td>
                                    <a href="{{ route("admin.user.edit", $v->user->id) }}">{{ $v->user->name }}</a>
                                </td>
                                <td class="font-weight-light">{{ $v->user->email }}</td>
                                <td>{{ $v->user->tel }}</td>
                                <td class="font-weight-light">{{ $v->qty }}</td>
                                <td class="font-weight-light">{{ $v->sum }}</td>
                                <td class="font-weight-light">{{ __("s.{$v->status}") }}</td>
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
