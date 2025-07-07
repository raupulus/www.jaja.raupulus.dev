<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('layouts.head')

<body>

@include('layouts.navbar')

<main class="main-content">
    <div class="container">

        @if(session('success'))
            <div class="main-alert alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="main-alert alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')

        @include('partials._beta')

    </div>
</main>

@include('layouts.footer')
@include('layouts.footer_meta')
@yield('css')
@yield('js')

</body>
</html>
