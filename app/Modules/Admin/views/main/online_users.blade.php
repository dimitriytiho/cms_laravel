@extends("{$viewPath}.layouts.admin")

@section('content')
    <section>
        <div class="row">
            <div class="col-12">
                <h5>
                    <span class="online-users-count">{{ $onlineUsers ? count($onlineUsers) : '0' }}</span>
                    <span> - @lang("{$lang}::s.now_on_the_site")</span>
                </h5>
            </div>
            <div class="col-12 online-users-list">
                @foreach($onlineUsers as $ip => $user)
                    @if (isset($user['id']))
                        <div>{{ $ip }} - <a href="{{ route("admin.user.edit", $user['id']) }}">{{ $user['name'] }}</a></div>
                    @else
                        <div>{{ $ip }}</div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
