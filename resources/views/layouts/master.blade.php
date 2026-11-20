<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('layouts.head')

<body>

<a href="#main-content" class="skip-link visually-hidden">Saltar al contenido principal</a>

@include('layouts.navbar')

<main id="main-content" class="main-content" tabindex="-1">
    <div class="container">

        @if(session('success'))
            <div class="main-alert alert alert-success" role="status" aria-live="polite">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="main-alert alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')

        @include('partials._beta')

    </div>
</main>

@include('layouts.footer')
@include('layouts.footer_meta')
@yield('js')

</body>
</html>
