@extends("{$viewPath}.layouts.admin")
{{--

Вывод контента

--}}
@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ isset($values->id) ? route("admin.{$route}.update", $values->id) : route("admin.{$route}.store") }}" method="post" class="needs-validation" novalidate>
                @if (isset($values->id))
                    @method('put')
                @endif
                @csrf
                @if (isset($values->id))
                    {!! $constructor::hidden('img', $values->img) !!}
                    <div class="row">
                        <div class="col-md-6 d-flex justify-content-center align-items-center img-view" id="dropzone-images">
                            <a href="{{ asset($values->img) }}" class="ml-3 mt-3" target="_blank">
                                @if ($values->img !== config("admin.img{$class}Default"))
                                    <i class="material-icons" id="img-remove" data-img="{{ $values->img }}" data-max-files="{{ config('admin.maxFilesOne') }}">clear</i>
                                @endif
                                <img src="{{ asset($values->img) }}" alt="@lang("{$lang}::f.img")">
                            </a>
                        </div>
                        {{--

                        Dropzone JS --}}
                        <div class="col-md-6">
                            <div id="dzOne" class="dropzone"></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            {!! $constructor::textarea('note', $values->note ?? null, null) !!}
                        </div>
                        <div class="col-md-6">
                            @if (!empty($statuses))
                                {!! $constructor::select('status', $statuses, $values->status ?? null) !!}
                            @endif
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        {!! $constructor::input('name', $values->name ?? null) !!}
                    </div>
                    <div class="col-md-6">
                        @if (!empty($roles))
                            {!! $constructor::select('role_id', $roles, $values->role->name ?? null, true, null, null, true, null, $roleIdAdmin) !!}
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! $constructor::input('email', $values->email ?? null) !!}
                    </div>
                    <div class="col-md-6">
                        {!! $constructor::input('tel', $values->tel ?? null, null) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {!! $constructor::input('address', $values->address ?? null, null) !!}
                    </div>
                </div>
                @if (isset($values->id))
                    <div>
                        @if (isset($values->id))
                            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse" data-target="#change-password" aria-expanded="false" aria-controls="change-password">{{ __("{$lang}::a.Change") . ' ' . \Illuminate\Support\Str::lower(__("{$lang}::f.password")) }}</button>
                        @endif
                        <div class="collapse mt-2" id="change-password">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! $constructor::input('password', null, null, 'password') !!}
                                </div>
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="w-100">
                                        {!! $constructor::input('password_confirmation', null, null, 'password') !!}
                                    </div>
                                    <div class="btn-flex">
                                        <button class="btn btn-primary mt-1 no-wrap" id="change-password-btn" data-user-id="{{ $values->id }}">@lang("{$lang}::a.Change")</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-6">
                            {!! $constructor::input('password', null, true, 'password') !!}
                        </div>
                        <div class="col-md-6">
                            {!! $constructor::input('password_confirmation', null, true, 'password') !!}
                        </div>
                    </div>
                @endif

                @if (isset($values->id))
                    <div class="row mt-3">
                        <div class="col-md-6">
                            {!! $constructor::input('id', $values->id, null, 'text', true, null, null, ['disabled' => 'true']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! $constructor::input('ip', $values->ip, null, 'text', true, null, null, ['disabled' => 'true']) !!}
                        </div>
                    </div>
                @endif

                @if (isset($values->updated_at) && isset($values->created_at))
                    <div class="row">
                        <div class="col-md-6">
                            {!! $constructor::input('updated_at', d($values->updated_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! $constructor::input('created_at', d($values->created_at, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => 'true'])!!}
                        </div>
                    </div>
                @endif

                    <div id="btn-sticky">
                        <button type="submit" class="btn btn-primary mt-3 btn-pulse">{{ isset($values->id) ? __("{$lang}::f.save") : __("{$lang}::f.submit") }}</button>
                    </div>
            </form>

            @if (isset($values->id))
                <form action="{{ route("admin.{$route}.destroy", $values->id) }}" method="post" class="text-right confirm-form">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-outline-primary mt-3 position-relative t--3 btn-pulse">@lang("{$lang}::s.remove")</button>
                </form>
            @endif
        </div>
    </div>
    {!! config('admin.sticky_submit') ? $constructor::stickyScript() : null !!}
@endsection
