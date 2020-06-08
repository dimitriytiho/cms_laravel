<div id="get-alert"></div>
{{--
<div class="row alert-js">
    <div class="col-md-10 offset-md-1">
        <div class="alert alert-danger py-4 px-5" role="alert">
            <span>Test</span>
        </div>
    </div>
</div>
<div class="row alert-js">
    <div class="col-md-10 offset-md-1">
        <div class="alert alert-success py-4 px-5" role="alert">
            <span>Test</span>
        </div>
    </div>
</div>
--}}
{{--

Сообщения об ошибках --}}
@if (session()->has('error') || isset($errors) && $errors->any())
    <div class="container">
        <div class="row mt-2">
            <div class="col">
                <div class="alert alert-danger alert-dismissible fade show py-3 px-4" role="alert">
                    @if ($errors->any())
                        <ul class="list-unstyled mb-0">
                            @foreach ($errors->all() as $error)
                                <li class="mt-1">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($errors->any() && session()->has('error'))
                        <br>
                    @endif
                    @if (session()->has('error'))
                        <span>{{ session('error') }}</span>
                        @php
                            session()->forget('error')
                        @endphp
                    @endif
                    <button type="button" class="close" data-dismiss="alert" aria-label="@lang("{$lang}::s.Close")">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
{{--

Сообщения об успехе --}}
@if (session()->has('message') || session()->has('success'))
    <div class="container">
        <div class="row mt-2">
            <div class="col">
                <div class="alert alert-success alert-dismissible fade show py-3 px-4" role="alert">
                    @if (session()->has('message'))
                        <span>{{ session('message') }}</span>
                    @endif
                    @if (session()->has('message') && session()->has('success'))
                        <br>
                    @endif
                    @if (session()->has('success'))
                        <span>{{ session('success') }}</span>
                        @php
                            session()->forget('success')
                        @endphp
                    @endif
                    <button type="button" class="close" data-dismiss="alert" aria-label="@lang("{$lang}::s.Close")">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
{{--

Сообщения информационные --}}
@if (session()->has('info'))
    <div class="container">
        <div class="row mt-2">
            <div class="col">
                <div class="alert alert-info alert-dismissible fade show py-3 px-4" role="alert">
                    <span>{{ session('info') }}</span>
                    @php
                        session()->forget('info')
                    @endphp
                    <button type="button" class="close" data-dismiss="alert" aria-label="@lang("{$lang}::s.Close")">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
{{--

Сообщения статусов --}}
@if (session()->has('status'))
    <div class="container">
        <div class="row mt-4">
            <div class="col">
                <div class="alert alert-info alert-dismissible fade show py-3 px-4" role="alert">
                    <span>{{ session('status') }}</span>
                    @php
                        session()->forget('status')
                    @endphp
                    <button type="button" class="close" data-dismiss="alert" aria-label="@lang("{$lang}::s.Close")">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
