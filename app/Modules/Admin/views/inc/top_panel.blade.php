<div class="row pb-4 top-panel">
    <div class="col d-flex justify-content-between align-items-center flex-wrap">
        <div class="btn-group" role="group" aria-label="nav">
            <a href="{{ route('admin.main') }}" class="btn btn-outline-primary d-flex align-items-center @if (Request::path() === env('APP_ADMIN')) disabled @endif btn-pulse">@lang("{$lang}::a.Dashboard")</a>
            @if (!empty($currentRoutesExclude))
                @foreach ($currentRoutesExclude as $v)
                    <a href="{{ route('admin.main') . $v['slug'] }}" class="btn btn-outline-primary btn-pulse @if (!empty($currentRoute['slug']) && $currentRoute['slug'] === $v['slug']) disabled @endif">@lang("{$lang}::a.{$v['title']}")</a>
                @endforeach
            @endif
        </div>
        <h1 class="font-weight-light text-secondary text-right my-2">
            @if (!empty($currentRoute))
                <span>@lang("{$lang}::a.{$currentRoute['title']}")</span>
            @endif
        </h1>
    </div>
</div>
