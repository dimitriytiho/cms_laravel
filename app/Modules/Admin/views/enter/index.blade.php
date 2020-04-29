@extends('layouts.enter')
{{--

Вывод контента

--}}
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 bg-white rounded shadow p-5 enter">
                <h1 class="font-weight-light text-secondary pb-2">@lang("{$lang}::s.login")</h1>
                <form method="post" action="{{ route('enter') }}" class="form-post mt-4 needs-validation needs-validation-no-submit" novalidate>
                    @csrf
                    {!! input('email', null, true, 'email', null) !!}
                    {!! input('password', null, true, 'password', null) !!}
                    <button type="submit" class="btn btn-primary mt-2 btn-pulse">@lang("{$lang}::f.submit")</button>
                </form>
            </div>
        </div>
    </div>
@endsection
