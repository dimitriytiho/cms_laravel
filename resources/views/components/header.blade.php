<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="{{ route('main') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="hamburger"></span>
            <span class="hamburger"></span>
            <span class="hamburger"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @if (env('APP_SHOP', null))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('catalog') }}">Каталог</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('main') }}/contacts">Контакты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact_us') }}">Связаться с нами</a>
                </li>
                @if (env('SITE_AUTH', null))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('enter') }}">Вход</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.main') }}">Админ</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>
</header>
