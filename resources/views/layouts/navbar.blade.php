<nav class="navbar">
    <div class="nav-wrapper">
        <a href="{{ route('index') }}" class="brand-logo">{{config('app.name')}}</a>
        <button class="mobile-menu-toggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-links">
            <ul>
                <li><a href="{{ route('index') }}" class="{{ request()->routeIs('index') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('api') }}" class="{{ request()->routeIs('api') ? 'active' : '' }}">Api</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Acerca de</a></li>
                <li><a href="https://raupulus.dev" target="_blank">Autor</a></li>

                @if (!auth()->guest())
                    <li><a href="{{ route('filament.admin.pages.dashboard') }}" class="{{ request()->routeIs('filament.admin.pages.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
