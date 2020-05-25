@extends('layouts.admin')

@section('content')
    @if ($onlineUsers)
        <section>
            <div class="row">
                <div class="col-12">
                    <h5>
                        <span class="online-users">{{ count($onlineUsers) }}</span>
                        <span> - @lang("{$lang}::s.now_on_the_site"):</span>
                    </h5>
                </div>
                <div class="col-12">
                    @foreach($onlineUsers as $ip => $user)
                        @if (isset($user['id']))
                            <a href="{{ route("admin.user.edit", $user['id']) }}" class="d-block">{{ $user['name'] }}</a>
                        @else
                            <div>{{ $ip }}</div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <div class="row">
            <div class="col my-4">
                <h5>@lang("{$lang}::s.no_users_now")</h5>
            </div>
        </div>
    @endif
@endsection
