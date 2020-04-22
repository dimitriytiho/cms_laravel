@extends('layouts.admin')
{{--

Вывод контента --}}
@section('content')
    @if (!empty($values))
        <div class="row">
            <div class="col">
                {!!

                $constructor::adminH2(__('a.notes'), 'mt-3 mb-3') !!}
                <form action="{{ route("admin.$route.update", $values->id) }}" method="post" class="needs-validation mb-4" novalidate>
                    @method('put')
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            {!! textarea('note', $values->note ?? null, null, true, null, null, null, 4) !!}
                        </div>
                        <div class="col-md-6">
                            {!! select('status', $statuses, $values->status ?? null) !!}
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary mt-2 btn-pulse">{{ __('f.save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                {!!

                $constructor::adminH2(__('a.Order'), 'mt-3 mb-3') !!}
                <table class="table table-striped mb-4">
                    <thead>
                    <tr>
                        <th scope="col" class="font-weight-light">ID</th>
                        <th scope="col" class="font-weight-light">{{ __('s.delivery') }}</th>
                        <th scope="col" class="font-weight-light">{{ __('s.delivery_sum') }}</th>
                        <th scope="col" class="font-weight-light">{{ __('s.discount') }}</th>
                        <th scope="col" class="font-weight-light">{{ __('s.discount_code') }}</th>
                        <th scope="col" class="font-weight-light">{{ __('s.qty') }}</th>
                        <th scope="col" class="font-weight-light">{{ __('s.sum') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row" class="font-weight-light">{{ $values->id }}</th>
                        <td class="font-weight-light">{{ $values->delivery }}</td>
                        <td class="font-weight-light">{{ $values->delivery_sum }}</td>
                        <td class="font-weight-light">{{ $values->discount }}</td>
                        <td class="font-weight-light">{{ $values->discount_code }}</td>
                        <td>{{ $values->qty }}</td>
                        <td>{{ $values->sum }}</td>
                    </tr>
                    </tbody>
                </table>

                <div class="table-responsive pt-3">
                    <table class="table table-striped">
                        <tbody>
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
                        </tbody>
                    </table>
                </div>
                @if($orderProducts)
                    {!!

                    $constructor::adminH2(__('a.Products'), 'mt-3 mb-3') !!}
                    <table class="table table-striped mb-4">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">{{ __('a.action') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('f.img') }}</th>
                            <th scope="col" class="font-weight-light">ID</th>
                            <th scope="col" class="font-weight-light">{{ __('f.title') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('f.slug') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('f.status') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('s.qty') }}</th>
                            <th scope="col" class="font-weight-light">{{ __('s.price') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orderProducts as $v)
                            <tr>
                                <th scope="row">
                                    <a href="{{ route("admin.product.edit", $v->product->id) }}" class="font-weight-light">
                                        <i aria-hidden="true" class="material-icons" title="{{ __('a.edit') }}">edit</i>
                                    </a>
                                </th>
                                <th class="font-weight-light">
                                    <img src="{{ $v->product->img }}" class="w-5" alt="{{ $v->product->title }}">
                                </th>
                                <th class="font-weight-light">{{ $v->product->id }}</th>
                                <th class="font-weight-light">{{ $v->product->title }}</th>
                                <th class="font-weight-light">{{ $v->product->slug }}</th>
                                <th class="font-weight-light">{{ __("s.{$v->product->status}") }}</th>
                                <th class="font-weight-light">{{ $v->qty }}</th>
                                <th class="font-weight-light">{{ $v->product->price }}</th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
                {!!

                $constructor::adminH2(__('s.user'), 'mt-3 mb-3') !!}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
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
