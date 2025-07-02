<nav class="navbar">
    <div class="nav-wrapper">
        <a href="{{ route('index') }}" class="brand-logo">
            <img src="{{ asset('images/logos/jaja-project-logo-square-small.webp') }}"
                 alt="Logotipo de {{config('app.name')}}">
            {{config('app.name')}}
        </a>

        <button class="mobile-menu-toggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-links">
            <ul>
                <li><a href="{{ route('index') }}" class="{{ request()->routeIs('index') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('page.show', 'api') }}" class="{{ (request()->routeIs('page.show') && request()->route('page')->slug === 'api') ? 'active' : '' }}">Api</a></li>
                <li><a href="{{ route('page.show', 'about') }}" class="{{ (request()->routeIs('page.show') && request()->route('page')->slug === 'about') ? 'active' : '' }}">Acerca de</a></li>

                <li><a href="https://raupulus.dev" target="_blank">Autor</a></li>

                @if (!auth()->guest())
                    <li><a href="{{ route('filament.admin.pages.dashboard') }}" class="{{ request()->routeIs('filament.admin.pages.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
