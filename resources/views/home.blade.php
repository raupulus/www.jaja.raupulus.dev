@extends('layouts.master')

@section('head')
    @vite(['resources/css/components.css', 'resources/css/general_stats.css'])
@endsection

@section('content')
    <section>
        <h1>Bienvenid@ a {{config('app.name')}}</h1>

        <div class="mw-800 m-auto">
            <img src="{{asset('images/portada-jaja-project.webp')}}" alt="Logo" class="logo">
        </div>

        <p class="mw-800 m-auto text-center text-destacado">
            ¿List@ para hacer <strong>reír</strong> (o intentarlo jeje), compartir y crear en comunidad?
        </p>

        <p class="mw-800 m-auto text-center text-secondary">
            En <strong>JaJa Project</strong> venimos a tomarnos la risa en serio (bueno, más o menos)
            creando una comunidad abierta donde los <strong>chistes</strong>, adivinanzas y preguntas tipo quiz cobran
            vida con tus
            ocurrencias.
        </p>
    </section>

    {{-- Estadísticas generales del contenido en la plataforma --}}
    @include('partials._general_stats')

    <section>
        <p class="mw-800 m-auto text-center text-secondary">
            Aquí puedes disfrutar del contenido más esporádico y subir tus propias invenciones.
        </p>

        <p class="mw-800 m-auto text-center text-secondary">
            Además tenemos una API para llevar el humor a tus bots, apps o proyectos.
        </p>

        <p class="mw-800 m-auto text-center text-destacado">
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

        <a href="{{route('page.show', 'api')}}" title="Enlace a la documentación de la api en {{config('app.name')}}"
           class="btn">Api Docs</a>
    </section>

    <section class="card">
        <h2 class="card-title">Bot para Twitch</h2>

        <p>
            WIP - En desarrollo. Estamos creando un bot para que puedas añadirlo a tu canal de Twitch y disfrutar del
            contenido directamente en el chat de tu canal utilizando el comando !chiste y !adivina con tu propio bot
            bajo tu control.
        </p>

        <a href="{{route('page.show', 'bot-twitch')}}" title="Enlace a la información sobre el bot de twitch"
           class="btn">Quiero mi BotHijo</a>
    </section>

    <section class="card">
        <h2 class="card-title">Buena Gente</h2>

        <p>
            La comunidad de jajajeros os agradece la colaboración y apoyo de todas esas personas que ayudan a que esto
            sea posible
        </p>

        <a href="{{route('page.show', 'agradecimientos')}}"
           title="Enlace a los agradecimientos de colaboradores y desarrolladores de {{config('app.name')}}"
           class="btn">JAgradece A...</a>
    </section>

    <section class="card">
        <h2 class="card-title">¡Envía el tuyo!</h2>

        <p>
            ¿Se te ha ocurrido algún chiste, pregunta tipo quiz o adivinanza?
        </p>

        <p>
            Comparte tu contenido con la comunidad para que todos lo disfrutemos pero asegúrate de que no sean
            inapropiados o incumplan <a href="{{route('page.show', 'normas')}}"
                                        title="Enlace a las normas de la comunidad en {{config('app.name')}}"
                                        target="_blank">las normas</a> .
        </p>

        <div>
            <form id="form-suggestion-send" action="{{route('suggestion.send')}}" method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="form-group">
                    <input type="text" name="nick" placeholder="Tu Nick (Sin @)"
                           class="form-control {{$errors->has('nick') ? 'form-group-error' : ''}}"
                           maxlength="25"
                           value="{{old('nick')}}">

                    <select name="type_id" class="form-control {{$errors->has('type_id') ? 'form-group-error' : ''}}">
                        @foreach(\App\Models\Type::all() as $type)
                            <option value="{{$type->id}}" {{(old('type_id') === $type->id) ? 'selected' : ''}}>
                                {{$type->name}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="{{$errors->has('image') ? 'form-group-error' : ''}}" id="image-grid-container">
                    <!-- Columna de la izquierda - Vista previa de la imagen -->
                    <div id="image-preview-container">
                        <div id="preview-placeholder" class="text-center">
                            Vista previa de imagen
                        </div>
                        <img id="preview-img" src="" alt="Vista previa">
                    </div>

                    <!-- Columna derecha: Label, Input y Errores -->
                    <div class="image-input-column">
                        <label for="content-image">
                            Imagen (Opcional).
                            <br/>
                            Tamaño máximo: 2MB. Formatos: JPG, PNG o WebP
                        </label>

                        <input id="content-image" type="file" name="image"
                               class="form-control"
                               accept="image/jpeg,image/png,image/webp">

                        <div id="file-error" class="text-danger"></div>
                    </div>
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
                        <a href="{{route('page.show', 'normas')}}"
                           title="Enlace a las normas de la comunidad en {{config('app.name')}}" target="_blank">las
                            Normas</a>
                        sobre contenido adecuado para la comunidad, acepto
                        <a href="{{route('page.show', 'politica-de-privacidad')}}"
                           title="Enlace a la política de privacidad en {{config('app.name')}}" target="_blank">la
                            Política de Privacidad</a>
                        y las
                        <a href="{{route('page.show', 'condiciones-de-uso')}}"
                           title="Enlace a las condiciones de uso en {{config('app.name')}}" target="_blank">las
                            Condiciones de Uso</a>
                    </label>

                </div>

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

@section('css')

    <style>
        #image-grid-container {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 15px;
            align-items: start;
            margin-bottom: 1rem;
        }

        #image-preview-container {
            width: 100%;
            max-width: 150px;
            height: 100px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background-color: #f9f9f9;
            cursor: pointer;
            grid-column: 1;
            grid-row: 1;
        }

        #preview-placeholder {
            font-size: 12px;
            color: #666;
            padding: 10px;
            text-align: center;
        }

        #preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .image-input-column {
            display: flex;
            flex-direction: column;
            gap: 8px;
            grid-column: 2;
            grid-row: 1;
            min-width: 0; /* Importante para que el contenedor pueda encogerse */
        }

        #image-grid-container .form-control {
            max-width: none;
            width: 100%;
            min-width: 0; /* Permite que el input se reduzca */
            box-sizing: border-box;
        }

        #content-image {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
        }

        #image-grid-container label {
            margin: 0;
            color: var(--color-texto-principal);
            word-wrap: break-word;
            hyphens: auto;
        }

        #file-error {
            display: none;
            margin-top: 0;
            color: #dc3545;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #image-grid-container {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            #image-preview-container {
                max-width: 200px;
                height: 120px;
                margin: 0 auto;
                grid-column: 1;
                grid-row: 1;
            }

            .image-input-column {
                grid-column: 1;
                grid-row: 2;
                min-width: 0;
            }
        }

        @media (max-width: 400px) {
            #image-grid-container {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 0;
                margin: 0 0 1rem 0;
            }

            #image-preview-container {
                max-width: 120px;
                height: 80px;
                margin: 0 auto;
                grid-column: 1;
                grid-row: 1;
            }

            .image-input-column {
                grid-column: 1;
                grid-row: 2;
                gap: 6px;
                min-width: 0;
            }

            #preview-placeholder {
                font-size: 11px;
                padding: 5px;
            }

            #image-grid-container label {
                font-size: 14px;
                line-height: 1.3;
            }
        }
    </style>

@endsection

@section('js')
    <script>
        function goToForm() {
            document.getElementById('form-suggestion-send').scrollIntoView({behavior: 'smooth'});
        }

        @if($errors->any())
        goToForm();
        @endif

        function handleImagePreview(e) {
            console.log(e);
            const input = e.target;
            const file = input.files[0];
            const errorElement = document.getElementById('file-error');
            const previewImg = document.getElementById('preview-img');
            const placeholder = document.getElementById('preview-placeholder');

            // Limpiar errores previos
            errorElement.style.display = 'none';

            if (file) {
                // Validar tamaño (2MB = 2097152 bytes)
                if (file.size > 2097152) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                    errorElement.textContent = `La imagen es demasiado grande (${fileSizeMB}MB). Máximo permitido: 2MB`;
                    errorElement.style.display = 'block';
                    input.value = '';

                    // Mostrar placeholder, ocultar imagen
                    placeholder.style.display = 'flex';
                    previewImg.style.display = 'none';
                    return false;
                }

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                // Si no hay archivo, mostrar placeholder
                placeholder.style.display = 'flex';
                previewImg.style.display = 'none';
            }

            return true;
        }


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


            /* Previsualización de imágenes */
            document.getElementById('content-image')?.addEventListener('change', handleImagePreview);
            document.getElementById('image-preview-container')?.addEventListener('click', () => {
                document.getElementById('content-image')?.click();
            });

        });

    </script>
@endsection
