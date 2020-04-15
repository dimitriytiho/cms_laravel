@if (Auth::check() && Auth::user()->admin())
    @php
        $link_dashboard = \App\Widgets\LinkDashboard::get();
    @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-1 px-0">
                <a href="{{ request()->root() . '/' . env('APP_ADMIN') }}" title="{{ __('a.Dashboard') }}" class="d-block bg-secondary py-1"></a>
            </div>
            <div class="col w-100 bg-dark py-1"></div>
            @if (!empty($link_dashboard))
                <div class="col-1 px-0">
                    <a href="{{ $link_dashboard }}" title="{{ __('c.Edit') }}" class="d-block bg-secondary py-1"></a>
                </div>
            @endif
        </div>
    </div>
@endif
{{--

<div class="container-fluid">
    <div class="row" style="height: 7px;">
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <a href="{{ Route('main') . '/' . env('APP_ADMIN') }}" title="{{ __('a.Dashboard') }}" style="width: 100px; background-color: #6c757d;"></a>

            <span style="width: 100%; background-color: #212529;"></span>

            @if (isset($link))
                <a href="{{ $link }}" title="{{ __('c.Edit') }}" style="width: 100px; background-color: #6c757d;"></a>
            @endif
        </div>
    </div>
</div>
--}}
