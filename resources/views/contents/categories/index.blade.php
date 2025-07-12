@extends('layouts.master')

@section('title', 'Listados de Categorías')
@section('description', 'Categorías disponibles en la comunidad de chistes/adivinanzas/quiz')
@section('keywords', 'categorías, categoría, comunidad, lista de categorías, listado de categorías')

@section('css')
    @vite(['resources/css/social_icons.css', 'resources/css/grid_categories.css'])
@endsection

@section('content')
    <h1 class="text-center">Categorías de contenido</h1>

    <section class="category-grid">
        @foreach($categories as $category)
            <a href="{{route('content.categoria.content.random', $category->slug)}}" class="category-card">
                <div class="category-card">
                    <div class="category-card-content {{ $category->image ? '' : 'no-image' }}">
                        @if($category->image)
                            <div class="category-card-image">
                                <img src="{{$category->urlImage}}" alt="{{$category->title}}">
                            </div>
                        @endif

                        <div class="category-card-text">
                            <h3 class="category-card-title">
                                {{ Str::limit($category->title, 80) }}
                            </h3>

                            @if($category->description)
                                <p class="category-card-description">
                                    {{ $category->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </section>

    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>
@endsection
