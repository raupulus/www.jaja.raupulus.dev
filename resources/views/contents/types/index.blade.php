@extends('layouts.master')

@section('head')
    @vite(['resources/css/social_icons.css', 'resources/css/horizontal_card.css'])
@endsection

@section('content')
    <h1 class="text-center">Tipos de contenido</h1>

    @foreach($types as $type)

        <div class="card-resume-horizontal">
            <div class="card-resume-left">
                <h3 class="card-resume-title">{{$type->name}}</h3>

                @if($type->image)
                    <div class="card-resume-image">
                        <img src="{{$type->urlImage}}" alt="{{$type->name}}">
                    </div>
                @endif
            </div>

            <div class="card-resume-content">
                <p class="card-resume-description">
                    {{$type->description}}
                </p>
                <div class="card-resume-meta">
                    Última actualización: {{$type->last_update_message}}
                </div>
            </div>

            <div class="card-resume-right">
                <span class="card-resume-badge">{{$type->groups_count}}</span>
                <a href="{{route('content.type.group.index', $type->slug)}}" class="card-resume-button">Ver Grupos</a>
            </div>
        </div>

    @endforeach

    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>
@endsection
