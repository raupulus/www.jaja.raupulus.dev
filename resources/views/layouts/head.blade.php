
@php
    ## Valores por defecto
    $pageTitle = trim($__env->yieldContent('title')) ?: config('app.name');
    $pageDescription = trim($__env->yieldContent('description')) ?: 'JaJa Project - La mejor comunidad para entretenimiento y compartir el humor';
    $pageKeywords = trim($__env->yieldContent('keywords')) ?: 'jaja project, entretenimiento, contenido, diversión, chistes, humor, adivinanzas, comunidad';
    $pageAuthor = trim($__env->yieldContent('author')) ?: config('app.author');
    $pageRobots = trim($__env->yieldContent('robots')) ?: 'index, follow';
    $pageCanonical = trim($__env->yieldContent('canonical')) ?: request()->url();

    ## Open Graph
    $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
    $ogTitle = trim($__env->yieldContent('og_title')) ?: $pageTitle;
    $ogDescription = trim($__env->yieldContent('og_description')) ?: $pageDescription;
    $ogUrl = trim($__env->yieldContent('og_url')) ?: request()->url();
    $ogLocale = trim($__env->yieldContent('og_locale')) ?: 'es_ES';
    $ogImageAlt = trim($__env->yieldContent('og_image_alt')) ?: $ogTitle;

    ## Twitter
    $twitterCard = trim($__env->yieldContent('twitter_card')) ?: 'summary_large_image';
    $twitterTitle = trim($__env->yieldContent('twitter_title')) ?: $ogTitle;
    $twitterDescription = trim($__env->yieldContent('twitter_description')) ?: $ogDescription;
    $twitterSite = trim($__env->yieldContent('twitter_site'));

    ## Otros
    $themeColor = trim($__env->yieldContent('theme_color'));

    ## Imágenes por defecto para redes sociales
    $defaultOgImage = asset('images/social/jaja-project-og-default.webp'); // 1200x630px
    $defaultTwitterImage = asset('images/social/jaja-project-twitter-default.webp'); // 1200x675px

    $ogImage = trim($__env->yieldContent('og_image')) ?: $defaultOgImage;
    $twitterImage = trim($__env->yieldContent('twitter_image')) ?: $defaultTwitterImage;
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- SEO Meta Tags --}}
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="author" content="{{ $pageAuthor }}">
    <meta name="robots" content="{{ $pageRobots }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $pageCanonical }}">

    {{-- Open Graph Meta Tags --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="{{ $ogLocale }}">

    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:alt" content="{{ $ogImageAlt }}">
    @endif

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="{{ $twitterCard }}">
    <meta name="twitter:title" content="{{ $twitterTitle }}">
    <meta name="twitter:description" content="{{ $twitterDescription }}">

    @if($twitterImage)
        <meta name="twitter:image" content="{{ $twitterImage }}">
    @endif

    @if($twitterSite)
        <meta name="twitter:site" content="{{ $twitterSite }}">
    @endif

    @if($themeColor)
        <meta name="theme-color" content="{{ $themeColor }}">
    @endif

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">

    {{-- Fonts --}}
    <link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}">

    {{-- CSS Assets --}}
    @vite(['resources/css/frontend.css', 'resources/css/buttons.css', 'resources/css/footer.css'])

    {{-- Additional Head Content --}}
    @yield('head')
</head>
