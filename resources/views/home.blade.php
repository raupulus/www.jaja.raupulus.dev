@extends('layouts.master')

@section('content')
    <section>
        <h1>Bienvenid@ a {{config('app.name')}}</h1>
        <p class="m-auto text-center">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
            ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat
            nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit
            anim id est laborum.
        </p>
    </section>

    <section class="card">
        <h2 class="card-title">Sección Destacada</h2>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua.
        </p>
        <a href="#" class="btn">Saber Más</a>
    </section>

    <section class="card">
        <h2 class="card-title">Bot para Twitch</h2>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua.
        </p>
        <a href="#" class="btn">Saber Más</a>
    </section>

    <section class="card">
        <h2 class="card-title">!Envía el tuyo!</h2>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua.
        </p>

        <div>
            <form id="form-suggestion-send" action="{{route('suggestion.send')}}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="form-group">
                    <input type="text" name="nick" placeholder="Tu Nick (Sin @)" class="form-control {{$errors->has('nick') ? 'form-group-error' : ''}}" value="{{old('nick')}}">

                    <select name="type_id" class="form-control {{$errors->has('type_id') ? 'form-group-error' : ''}}">
                        @foreach(\App\Models\Type::all() as $type)
                            <option value="{{$type->id}}" {{(old('type_id') === $type->id) ? 'selected' : ''}}>{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group {{$errors->has('image') ? 'form-group-error' : ''}}">
                    <label for="content-image">Imagen (Opcional, 2MB max.)</label>
                    <input id="content-image" type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="form-group {{$errors->has('title') ? 'form-group-error' : ''}}">
                    <input type="text" name="title" placeholder="Título" class="form-control" required value="{{old('title')}}">
                </div>

                <div class="form-group {{$errors->has('content') ? 'form-group-error' : ''}}">
                    <textarea name="content" class="form-control" placeholder="Escribe aquí el contenido" required>{{old('description')}}</textarea>
                </div>

                @php
                    $nonThrottleErrors = collect($errors->all())->filter(function($error) {
                        return !Str::contains($error, 'espera');
                    });
                @endphp

                @if($nonThrottleErrors->isNotEmpty())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($nonThrottleErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                @if($errors->has('throttle'))
                    <div class="alert alert-warning throttle-alert" id="throttleAlert">
                        <div class="throttle-icon" style="text-align: right;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f5a623" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="timer-icon">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="throttle-message">
                            <strong>Control de peticiones:</strong>
                            <p>Por favor, espera
                                <span id="throttleCounter" data-seconds="{{ preg_replace('/[^0-9]/', '', $errors->first('throttle')) }}">
                                    {{ preg_replace('/[^0-9]/', '', $errors->first('throttle')) }}
                                </span> segundos antes de enviar.
                            </p>
                        </div>
                    </div>
                @endif



                <button type="submit" class="btn">Enviar</button>
            </form>
        </div>
    </section>

    <section class="box-card-content">
        <h2>Algunos contenidos</h2>

        @foreach($contents as $content)
            <div class="card card-content">
                @if ($content->image)
                    <div class="card-content-image">
                        <img src="{{$content->urlImage}}" title="{{$content->title}}" alt="{{$content->title}}"/>
                    </div>
                @endif

                <div class="card-content-body">
                    <h3>{{$content->title}}</h3>

                    <p>
                        {{$content->content}}
                    </p>
                </div>

                <div class="card-content-meta">
                    <span>{{$content->group?->title}}</span>
                    <span>Por: {{$content->uploader}}</span>
                </div>

            </div>
        @endforeach
    </section>
@endsection

@section('js')
    <script>
        function goToForm() {
            document.getElementById('form-suggestion-send').scrollIntoView({behavior: 'smooth'});
        }

        @if($errors->any())
            goToForm();
        @endif


        document.addEventListener('DOMContentLoaded', function() {
            const throttleCounter = document.getElementById('throttleCounter');

            if (throttleCounter) {
                let seconds = parseInt(throttleCounter.dataset.seconds, 10);

                if (!isNaN(seconds) && seconds > 0) {
                    const submitButton = document.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.classList.add('disabled');
                    }

                    // Inicio contador
                    const countdownInterval = setInterval(function() {
                        seconds--;
                        throttleCounter.textContent = seconds;

                        // Cuando llega a cero
                        if (seconds <= 0) {
                            clearInterval(countdownInterval);

                            // Habilito botón de envío
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.classList.remove('disabled');
                            }

                            // Oculto el mensaje de throttle con animación
                            const throttleAlert = document.getElementById('throttleAlert');
                            if (throttleAlert) {
                                throttleAlert.classList.add('fade-out');
                                setTimeout(function() {
                                    throttleAlert.style.display = 'none';
                                }, 500);
                            }
                        }
                    }, 1000);
                }
            }
        });

    </script>
@endsection
