@extends('layouts.master')

@section('content')
    <section>
        <h1>Bienvenid@ a {{config('app.name')}}</h1>

        <div>
            <img src="{{asset('images/portada-jaja-project.webp')}}" alt="Logo" class="logo">
        </div>

        <p class="m-auto text-center text-destacado">
            ¿List@ para hacer <strong>reír</strong> (o intentarlo jeje), compartir y crear en comunidad?
        </p>

        <p class="m-auto text-center text-secondary">
            En <strong>JaJa Project</strong> venimos a tomarnos la risa en serio (bueno, más o menos)
            creando una comunidad abierta donde los <strong>chistes</strong>, adivinanzas y preguntas tipo quiz cobran vida con tus
            ocurrencias.
        </p>

        <p class="m-auto text-center text-secondary">
            Aquí puedes disfrutar del contenido más esporádico y subir tus propias invenciones.
        </p>

        <p class="m-auto text-center text-secondary">
            Además tenemos una API para llevar el humor a tus bots, apps o proyectos.
        </p>

        <p class="m-auto text-center text-destacado">
            ¿Contará tu <strong>bot</strong> mejores chistes que tú?
        </p>
    </section>

    <section class="card">
        <h2 class="card-title">Documentación API</h2>

        <p>
            Tenemos una api documentada para que puedas crear tus propios bots o aplicaciones. Puedes utilizar un
            endpoint sin ningún tipo de registro para obtener contenido aleatorio o solicitar unirte a nuestra comunidad
            para acceder a más contenido, más ratio de consultas y obtener contenido filtrado.
        </p>

        <a href="#" class="btn">Api Docs</a>
    </section>

    <section class="card">
        <h2 class="card-title">Bot para Twitch</h2>

        <p>
            WIP - En desarrollo. Estamos creando un bot para que puedas añadirlo a tu canal de Twitch y disfrutar del
            contenido directamente en el chat de tu canal utilizando el comando !chiste y !adivina con tu propio bot
            bajo tu control.
        </p>

        <a href="#" class="btn">Quiero mi BotHijo</a>
    </section>

    <section class="card">
        <h2 class="card-title">¡Envía el tuyo!</h2>

        <p>
            ¿Se te ha ocurrido algún chiste, pregunta tipo quiz o adivinanza?
        </p>

        <p>
            Comparte tu contenido con la comunidad para que todos lo disfrutemos pero asegúrate de que no sean
            inapropiados o incumplan <a href="#" target="_blank">las normas</a> .
        </p>

        <div>
            <form id="form-suggestion-send" action="{{route('suggestion.send')}}" method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="form-group">
                    <input type="text" name="nick" placeholder="Tu Nick (Sin @)"
                           class="form-control {{$errors->has('nick') ? 'form-group-error' : ''}}"
                           value="{{old('nick')}}">

                    <select name="type_id" class="form-control {{$errors->has('type_id') ? 'form-group-error' : ''}}">
                        @foreach(\App\Models\Type::all() as $type)
                            <option
                                value="{{$type->id}}" {{(old('type_id') === $type->id) ? 'selected' : ''}}>{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group {{$errors->has('image') ? 'form-group-error' : ''}}">
                    <label for="content-image">Imagen (Opcional, 2MB max.)</label>
                    <input id="content-image" type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="form-group {{$errors->has('title') ? 'form-group-error' : ''}}">
                    <input type="text" name="title" placeholder="Título" class="form-control" required
                           value="{{old('title')}}">
                </div>

                <div class="form-group {{$errors->has('content') ? 'form-group-error' : ''}}">
                    <textarea name="content" class="form-control" placeholder="Escribe aquí el contenido"
                              required>{{old('description')}}</textarea>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="#f5a623" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="timer-icon">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="throttle-message">
                            <strong>Control de peticiones:</strong>
                            <p>Por favor, espera
                                <span id="throttleCounter"
                                      data-seconds="{{ preg_replace('/[^0-9]/', '', $errors->first('throttle')) }}">
                                    {{ preg_replace('/[^0-9]/', '', $errors->first('throttle')) }}
                                </span> segundos antes de enviar.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Textos legales --}}
                <div>
                    <input id="terms" type="checkbox" name="terms" required>
                    <label for="terms">
                        Confirmo que he leído
                        <a href="#" target="_blank">las Normas</a>
                        de contenido adecuado para la comunidad y acepto
                        <a href="#" target="_blank">la Política de Privacidad</a>.
                    </label>

                </div>

                <button type="submit" class="btn">Enviar</button>
            </form>
        </div>
    </section>

    <section class="card">
        <h2 class="card-title">Buena Gente</h2>

        <p>
            La comunidad de jajajeros os agradece la colaboración y apoyo de todas esas personas que ayudan a que esto
            sea posible
        </p>

        <a href="#" class="btn">JAgradecimientos</a>
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


        document.addEventListener('DOMContentLoaded', function () {
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
                    const countdownInterval = setInterval(function () {
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
                                setTimeout(function () {
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
