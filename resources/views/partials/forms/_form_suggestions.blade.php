<form id="form-suggestion-send" action="{{route('suggestion.send')}}" method="POST"
      enctype="multipart/form-data">

    @csrf

    <div class="form-group">
        <input type="text" name="nick" placeholder="Tu Nick (Sin @)"
               class="form-control {{$errors->has('nick') ? 'form-group-error' : ''}}"
               maxlength="25"
               value="{{old('nick')}}">

        <select name="type_id" class="form-control {{$errors->has('type_id') ? 'form-group-error' : ''}}">
            @foreach(\App\Models\Type::select(['id', 'slug', 'name'])->orderBy('id')->get() as $type)
                <option value="{{$type->id}}" {{(old('type_id', 1) == $type->id) ? 'selected' : ''}}
                        data-slug="{{$type->slug}}">
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
               maxlength="255"
               value="{{old('title')}}">
    </div>

    <div class="form-group {{$errors->has('content') ? 'form-group-error' : ''}}">
        <textarea name="content" class="form-control" placeholder="Escribe aquí el contenido"
                  maxlength="1024"
                  style="field-sizing: content;"
                  required>{{old('content')}}</textarea>
    </div>


    <div class="box-quiz-answers">
        <div class="form-group text-center">
            <small>
                Añade al menos dos respuestas y selecciona la que sea correcta.
            </small>
        </div>

        <div class="form-group">
            <span>¿Correcta?</span>
            <span class="text-center">Respuestas</span>
        </div>

        @foreach(range(1,4) as $pos)
            <div class="form-group-answer form-group {{$errors->has('answer' . $pos) ? 'form-group-error' : ''}}">
                <div class="box-answer-check">
                    <span class="answer-check answer-check-on hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none">
                            <path d="M5 13L9 17L19 7" stroke="green" stroke-width="2" fill="none"/>
                        </svg>
                    </span>

                    <span class="answer-check answer-check-off">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none">
                          <path d="M6 6L18 18" stroke="red" stroke-width="2"/>
                          <path d="M6 18L18 6" stroke="red" stroke-width="2"/>
                        </svg>
                    </span>
                </div>

                <input class="answer-checkbox hidden" type="checkbox" name="answer{{$pos}}_correct">
                <input type="text" name="answer{{$pos}}" maxlength="255">
            </div>
        @endforeach
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


    {{-- Google Recaptcha --}}
    @if(config('google.recaptcha.site_key'))
        <div>
            <input type="hidden" name="g_recaptcha" id="g_recaptcha">
        </div>
    @endif

    <button type="submit" class="btn">😜 Enviar 😝</button>
</form>
