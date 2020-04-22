<header class="container-fluid text-white header">
    <div class="row">
        <div class="d-flex align-items-center bg-light shadow-sm py-1 transition a-secondary aside-width-change" id="header__icon"{!! $asideWidth !!}>
            <a href="{{ route('index') }}" class="d-flex align-items-center">
                <img src="{{ asset(config('add.img') . '/omegakontur/touch-icon-iphone-retina.png') }}" class="pr-3 pl-2" alt="{{ env('APP_NAME') }}">
                <span class="aside-text fadein"{!! $asideText !!}>{{ __('a.Website') }}</span>
            </a>
        </div>

        <div class="col bg-primary d-flex justify-content-between shadow py-1 a-white header__left">
            <ul class="nav">
                <li class="nav-item d-flex align-items-center">
                    <a href="#" class="nav-link d-flex align-items-center aside-width">
                        <i aria-hidden="true" class="material-icons aside-width">menu</i>
                    </a>
                </li>
            </ul>

            <ul class="nav justify-content-end header__right">
                 {{--<li class="nav-item d-flex align-items-center">
                   <a href="#" class="nav-link d-flex align-items-center">
                       <i aria-hidden="true" class="material-icons">notifications</i>
                   </a>
                </li>--}}
                @if ($excludeCurrentLocale)
                    <li class="nav-item d-flex align-items-center">
                        <a href="{{ route('admin.locale', $excludeCurrentLocale[0] ?? null) }}" class="nav-link d-flex align-items-center">{{ !empty($excludeCurrentLocale[0]) ? \Illuminate\Support\Str::ucfirst($excludeCurrentLocale[0]) : null }}</a>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a href="{{ route('admin.user.edit', auth()->user()->id) }}" class="nav-link d-flex align-items-center dropdown-click" title="{{ __('c.welcome') . auth()->user()->name . '!' }}">
                        <img src="{{ asset(auth()->user()->img) }}" class="dropdown-click" id="avatar" alt="Avatar">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('admin.user.edit', auth()->user()->id) }}" class="dropdown-item text-secondary my-1">{{ __('a.Profile') }}</a>
                        <a href="{{ route('admin.logout') }}" class="dropdown-item text-secondary my-1">{{ __('a.Exit') }}</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    {{--

    Меню для мобильных --}}
    @if($menuAsideChunk)
        <div class="js-none" id="menu-mobile">
            <div class="row my-4">
                @foreach($menuAsideChunk as $chunk)
                    @if($chunk)
                        <div class="col-sm-6 col-12">
                            @foreach($chunk as $elMenu)
                                @if(!$elMenu['parent_id'] && !(in_array($elMenu['controller'], config('admin.editor_section_banned')) && !$isAdmin))
                                    <a href="{{ route('admin.main') . $elMenu['slug'] }}" class="d-block py-1">{{ __("a.{$elMenu['title']}") }}</a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</header>
