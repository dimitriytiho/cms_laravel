<aside class="aside a-primary-light aside-width-change" style="width: {{ $asideWidth }};">

    <div class="d-flex align-items-center py-1 a-secondary aside-width-change aside__header" id="header__icon" style="width: {{ $asideWidth }};">
        <a href="{{ session()->get('back_link_site', route('index')) }}" title="@lang("{$lang}::a.Website")">
            <img src="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone-retina.png') }}" alt="{{ config('add.name') }}">
            <span class="aside-text fadein">@lang("{$lang}::a.Website")</span>
        </a>
    </div>

    <ul class="list-unstyled py-2 sticky-top">
        @if (!empty($menuAside))
            @foreach ($menuAside as $v)
                @if (!$v['parent_id'] && !(in_array($v['controller'], config('admin.editor_section_banned')) && !$isAdmin) && !(!in_array($v['controller'], config('admin.cashier_section_allow')) && !$isAdmin))
                    <li class="position-relative py-2">
                        <a href="{{ route('admin.main') . $v['slug'] }}" class="d-flex align-items-center py-1 px-2 aside__a @if ($v['request'] === 'main' && request()->url() === route('admin.main') || HelpersAdd::inRequestStr($v['request']))active @endif" data-title="{{ $v['controller'] ?: $v['title'] }}">
                            <i class="{{ $v['item'] }}" title="@lang("{$lang}::a.{$v['title']}")"></i>
                            {{--<i aria-hidden="true" class="material-icons pr-3" title="@lang("{$lang}::a.{$v['title']}")">{{ $v['item'] }}</i>--}}
                            <span class="aside-text fadein">@lang("{$lang}::a.{$v['title']}")</span>
                        </a>
                        @if (isset($v['count']) && Schema::hasTable($v['count']))
                            <span class="counter-small">{{ DB::table($v['count'])->count() }}</span>
                        @endif
                    </li>
                @endif
            @endforeach
        @endif
    </ul>
</aside>
