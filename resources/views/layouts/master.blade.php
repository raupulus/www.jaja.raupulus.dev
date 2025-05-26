<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('layouts.head')

<body>

@include('layouts.navbar')

<main class="main-content">
    <div class="container">

        @yield('content')

    </div>
</main>

@include('layouts.footer')
@include('layouts.footer_meta')
@yield('css')
@yield('js')

</body>
</html>
