<header class="container-fluid text-white header">
    <div class="row">
        <div class="col bg-primary d-flex justify-content-between shadow py-1 a-white header__left transition {{--aside-margin-change--}}" {{--style="margin-left: {{ $asideWidth }};"--}}>
            <ul class="nav">
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link d-flex align-items-center">
                        <i aria-hidden="true" class="material-icons cur aside-width">menu</i>
                    </a>
                </li>
            </ul>

            <ul class="nav justify-content-end header__right">
                 {{--<li class="nav-item d-flex align-items-center">
                   <a href="#" class="nav-link d-flex align-items-center">
                       <i aria-hidden="true" class="material-icons">notifications</i>
                   </a>
                </li>--}}

                @if (config('add.online_users'))
                    <li class="nav-item d-flex align-items-center online-users">
                        <a href="{{ route('admin.online_users') }}" class="nav-link d-flex align-items-center position-relative" title="@lang("{$lang}::s.online_users")">
                            <span class="material-icons">people_alt</span>
                            <span class="counter-small text-white online-users-count">{{ $onlineUsers ? count($onlineUsers) : '0' }}</span>
                        </a>
                    </li>
                @endif

                @if ($excludeCurrentLocale)
                    <li class="nav-item d-flex align-items-center">
                        <a href="{{ route('admin.locale', $excludeCurrentLocale[0] ?? null) }}" class="nav-link d-flex align-items-center" title="@lang("{$lang}::s.language")">{{ !empty($excludeCurrentLocale[0]) ? \Illuminate\Support\Str::ucfirst($excludeCurrentLocale[0]) : null }}</a>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a href="{{ route('admin.user.edit', auth()->user()->id) }}" class="nav-link d-flex align-items-center dropdown-click" title="{{ auth()->user()->name  . __("{$lang}::s.welcome") }}">
                        <img src="{{ asset(auth()->user()->img) }}" class="dropdown-click" id="avatar" alt="Avatar">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('admin.user.edit', auth()->user()->id) }}" class="dropdown-item text-secondary my-1">@lang("{$lang}::a.Profile")</a>
                        <a href="{{ route('admin.logout') }}" class="dropdown-item text-secondary my-1">@lang("{$lang}::a.Exit")</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    {{--

    Меню для мобильных --}}
    @if ($menuAsideChunk)
        <div class="js-none" id="menu-mobile">
            <div class="row my-4">
                @foreach ($menuAsideChunk as $chunk)
                    @if ($chunk)
                        <div class="col-sm-6 col-12">
                            @foreach ($chunk as $elMenu)
                                @if (!$elMenu['parent_id'] && !(in_array($elMenu['controller'], config('admin.editor_section_banned')) && !$isAdmin))
                                    <a href="{{ route('admin.main') . $elMenu['slug'] }}" class="d-block py-1">@lang("{$lang}::a.{$elMenu['title']}")</a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</header>
