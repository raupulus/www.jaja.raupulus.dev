@extends('layouts.master')

@section('title', 'Contenidos aleatorios para la categoría ' . $category->title)
@section('description', 'Listado de contenido aleatorio para la categoría ' . $category->title)
@section('keywords', $category->title . ',categoría, listado, category, chistes, adivinanzas, quiz, aleatorio')

@section('css')
    @vite(['resources/css/social_icons.css'])
@endsection

@section('content')
    <div class="text-left">
        <a href="{{route('content.categories.index')}}">
            <span class="icon-back"><-</span>
            Ir a las Categorías
        </a>
    </div>

    <div>
        <div>
            <h1>{{$category->title}}</h1>
            <p>
                {!! $category->description !!}
            </p>

            @if($category->image)
                <img src="{{$category->urlimage}}" alt="{{$category->title}}" style="max-width: 500px;">
            @endif
        </div>

        @if($contents->count())
        <div id="box-btn-random">
            <div class="btn-random-content">
                <a href="{{route('content.group.content.random', $category->slug)}}#box-btn-random"
                   onclick="window.location.reload(); return false;">
                    <div class="wheel">
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                    </div>
                    <span class="text">Random</span>
                </a>
            </div>
        </div>

        <div>
            @include('contents.partials._contents', ['contents' => $contents])
        </div>

        @else

            <h2>No hay contenidos para esta categoría</h2>

        @endif
    </div>


    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>

@endsection
