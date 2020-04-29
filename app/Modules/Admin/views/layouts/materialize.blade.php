{{--

Materialize css шаблон

--}}
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('touch-icon-iphone.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('touch-icon-ipad.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('touch-icon-iphone-retina.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('touch-icon-ipad-retina.png') }}">
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {!! $getMeta !!}
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"   media="screen,projection">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/enter.css') }}" media="all">
    @include('components.warning')
</head>
<body class="blue-grey lighten-1">
<div id="app">
    <a href="{{ route('index') }}" class="blue-grey-text tooltipped cur" data-position="right" data-tooltip="@lang("{$lang}::c.home")">
        <i class="material-icons">apps</i>
    </a>
    {{--
    <nav class="header">
        <div class="nav-wrapper blue-grey lighten-1">
            <div class="container">
                <a href="{{ route('index') }}" class="brand-logo">
                    <i class="material-icons">apps</i>
                </a>
            </div>
        </div>
    </nav>
    --}}
    <div id="wrapper">
        <main class="main">
            <div class="container">
                <div class="row">
                    <div class="col s6 offset-s3 white z-depth-3 rounded">
                        <div class="row">
                            <div class="col s10 offset-s1">
                                <h1 class="blue-grey-text">@lang("{$lang}::s.login")</h1>
                                <form method="post" action="{{ route('enter') }}">
                                    @csrf
                                    @if (empty($auth_view))
                                        <div class="input-field">
                                            <input type="email" class="validate" name="email" id="email" value = "{{ old('email') }}" required autocomplete="email">
                                            <label for="email">@lang("{$lang}::forms.Email")</label>
                                        </div>
                                    @elseif($auth_view == 'confirm')
                                        <div class="input-field">
                                            <input type="text" class="validate" name="confirm" id="confirm" required autocomplete="confirm">
                                            <label for="confirm">@lang("{$lang}::forms.Verification_code")</label>
                                        </div>
                                    @elseif($auth_view == 'password')
                                        <div class="input-field">
                                            <input type="password" class="validate" name="password" id="password" required autocomplete="current-password">
                                            <label for="password">@lang("{$lang}::forms.Password")</label>
                                        </div>
                                        <div>
                                            <label for="remember">
                                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <span>@lang("{$lang}::auth.Remember_me")</span>
                                            </label>
                                        </div>
                                    @endif
                                    <div class="main__btn">
                                        <button  type="submit" class="btn waves-effect waves-light blue-grey lighten-1">@lang("{$lang}::forms.Send")</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="bottom-block"></div>
</div>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        {{--

        Служебные сообщения в тосте --}}
        @if (isset($errors) && $errors->any())
            @foreach( $errors->all() as $v )
                M.toast({html: '{{ $v }}'});
            @endforeach
        @endif
        @if (session()->has('error'))
            M.toast({html: '{{ session("error") }}'});
            @php
                session()->forget('error')
            @endphp
        @endif
        @if (session()->has('message'))
            M.toast({html: '{{ session("message") }}'});
        @endif
        @if (session()->has('success'))
            M.toast({html: '{{ session("success") }}'});
            @php
                session()->forget('success')
            @endphp
        @endif
    });
</script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.tooltipped');
        var instances = M.Tooltip.init(elems, {margin: 1});
    });
</script>
</body>
</html>
