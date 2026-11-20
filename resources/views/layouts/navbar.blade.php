<header class="site-header">
    <nav class="navbar" aria-label="Navegación principal">
        <div class="nav-wrapper">
            <div class="nav-header-row">
                <a href="{{ route('index') }}" class="brand-logo">
                    <img src="{{ asset('images/logos/jaja-project-logo-square-small.webp') }}"
                         alt="Logotipo de {{config('app.name')}}"
                         width="30"
                         height="30"
                         decoding="async">
                    <span>{{config('app.name')}}</span>
                </a>

                <button type="button"
                        class="mobile-menu-toggle"
                        aria-label="Abrir menú"
                        aria-expanded="false"
                        aria-controls="primary-nav-links">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </button>
            </div>

            <div id="primary-nav-links" class="nav-links">
                <ul>
                    @php
                        $currentPageSlug = request()->route('page')?->slug;
                        $isHomeActive = request()->routeIs('index');
                        $isGroupsActive = request()->routeIs('content.groups.*', 'content.type.group.*', 'content.group.*', 'content.types.*');
                        $isCategoriesActive = request()->routeIs('content.categories.*', 'content.categoria.*');
                        $isCollaboratorsActive = request()->routeIs('collaborator.*');
                        $isApiActive = request()->routeIs('page.show') && $currentPageSlug === 'api';
                        $isAboutActive = request()->routeIs('page.show') && $currentPageSlug === 'about';
                    @endphp

                    <li>
                        <a href="{{ route('index') }}"
                           class="{{ $isHomeActive ? 'active' : '' }}"
                           @if($isHomeActive) aria-current="page" @endif>Inicio</a>
                    </li>
                    <li>
                        <a href="{{ route('content.groups.index') }}"
                           class="{{ $isGroupsActive ? 'active' : '' }}"
                           @if($isGroupsActive) aria-current="page" @endif>Grupos</a>
                    </li>
                    <li>
                        <a href="{{ route('content.categories.index') }}"
                           class="{{ $isCategoriesActive ? 'active' : '' }}"
                           @if($isCategoriesActive) aria-current="page" @endif>Categorías</a>
                    </li>
                    <li>
                        <a href="{{ route('collaborator.index') }}"
                           class="{{ $isCollaboratorsActive ? 'active' : '' }}"
                           @if($isCollaboratorsActive) aria-current="page" @endif>Colaboradores</a>
                    </li>
                    <li>
                        <a href="{{ route('page.show', 'api') }}"
                           class="{{ $isApiActive ? 'active' : '' }}"
                           @if($isApiActive) aria-current="page" @endif>API</a>
                    </li>
                    <li>
                        <a href="{{ route('page.show', 'about') }}"
                           class="{{ $isAboutActive ? 'active' : '' }}"
                           @if($isAboutActive) aria-current="page" @endif>Acerca de</a>
                    </li>
                    <li><a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer">Autor</a></li>

                    @auth
                        @if (auth()->user()->isAdmin)
                            <li>
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                   class="{{ request()->routeIs('filament.admin.*') ? 'active' : '' }}">Admin</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('filament.panel.pages.dashboard') }}"
                                   class="{{ request()->routeIs('filament.panel.*') ? 'active' : '' }}">Panel</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
