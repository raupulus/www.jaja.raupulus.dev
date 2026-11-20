<form id="form-suggestion-send"
      action="{{route('suggestion.send')}}"
      method="POST"
      enctype="multipart/form-data"
      aria-labelledby="suggestion-form-heading">

    @csrf

    <p class="form-required-hint">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>

    <div class="form-group">
        {{-- Nick --}}
        <div class="form-field">
            <label for="suggestion-nick">Tu nick (opcional, sin @)</label>
            <input id="suggestion-nick"
                   type="text"
                   name="nick"
                   placeholder="Ej: raupulus"
                   class="form-control {{$errors->has('nick') ? 'form-group-error' : ''}}"
                   maxlength="25"
                   autocomplete="nickname"
                   autocorrect="off"
                   autocapitalize="none"
                   spellcheck="false"
                   value="{{old('nick')}}"
                   aria-invalid="{{ $errors->has('nick') ? 'true' : 'false' }}"
                   @error('nick') aria-describedby="error-nick" @enderror>
            @error('nick')
                <span id="error-nick" class="field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        {{-- Selector de Tipo --}}
        <div class="form-field">
            <label for="suggestion-type">Tipo de contenido <span aria-hidden="true">*</span></label>
            <select id="suggestion-type"
                    name="type_id"
                    required
                    class="form-control {{$errors->has('type_id') ? 'form-group-error' : ''}}"
                    aria-invalid="{{ $errors->has('type_id') ? 'true' : 'false' }}"
                    @error('type_id') aria-describedby="error-type_id" @enderror>
                @foreach(\App\Models\Type::select(['id', 'slug', 'name'])->orderBy('id')->get() as $type)
                    <option value="{{$type->id}}" {{(old('type_id', 1) == $type->id) ? 'selected' : ''}}
                            data-slug="{{$type->slug}}">
                        {{$type->name}}
                    </option>
                @endforeach
            </select>
            @error('type_id')
                <span id="error-type_id" class="field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Selector de grupos --}}
    <div class="form-group">
        <div class="form-field">
            <label for="suggestion-group">Grupo (opcional)</label>
            <select id="suggestion-group"
                    name="group_id"
                    class="form-control {{$errors->has('group_id') ? 'form-group-error' : ''}}"
                    aria-invalid="{{ $errors->has('group_id') ? 'true' : 'false' }}"
                    @error('group_id') aria-describedby="error-group_id" @enderror>
                <option value="">Selecciona un grupo</option>
                @foreach(\App\Models\Group::select(['id', 'type_id', 'slug', 'title'])->orderBy('title')->get() as $group)
                    <option value="{{$group->id}}" {{(old('group_id') == $group->id) ? 'selected' : ''}}
                    data-type_id="{{$group->type_id}}"
                    data-slug="{{$group->slug}}">
                        {{$group->title}}
                    </option>
                @endforeach
            </select>
            @error('group_id')
                <span id="error-group_id" class="field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="{{$errors->has('image') ? 'form-group-error' : ''}}" id="image-grid-container">
        <!-- Columna de la izquierda - Vista previa de la imagen -->
        <label for="content-image" id="image-preview-container">
            <span id="preview-placeholder" class="text-center">
                Vista previa de imagen
            </span>
            <img id="preview-img" class="hidden" alt="Vista previa de la imagen seleccionada">
        </label>

        <!-- Columna derecha: Label, Input y Errores -->
        <div class="image-input-column">
            <label for="content-image">
                Imagen (opcional).
                <br/>
                Tamaño máximo: 2MB. Formatos: JPG, PNG o WebP
            </label>

            <input id="content-image" type="file" name="image"
                   class="form-control"
                   accept="image/jpeg,image/png,image/webp"
                   aria-invalid="{{ $errors->has('image') ? 'true' : 'false' }}"
                   @error('image') aria-describedby="error-image" @enderror>

            <div id="file-error" class="text-danger" role="alert" aria-live="assertive"></div>
            @error('image')
                <span id="error-image" class="field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>


    <div class="form-group">
        <div class="form-field">
            <label for="suggestion-title">Título <span aria-hidden="true">*</span></label>
            <input id="suggestion-title"
                   type="text"
                   name="title"
                   placeholder="Título representativo del contenido"
                   class="form-control {{$errors->has('title') ? 'form-group-error' : ''}}"
                   required
                   maxlength="255"
                   value="{{old('title')}}"
                   aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}"
                   @error('title') aria-describedby="error-title" @enderror>
            @error('title')
                <span id="error-title" class="field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <div class="form-field">
            <label for="suggestion-content">Contenido <span aria-hidden="true">*</span></label>
            <textarea id="suggestion-content"
                      name="content"
                      class="form-control textarea-autosize {{$errors->has('content') ? 'form-group-error' : ''}}"
                      placeholder="Escribe aquí el chiste, adivinanza o pregunta"
                      rows="4"
                      maxlength="1024"
                      required
                      aria-invalid="{{ $errors->has('content') ? 'true' : 'false' }}"
                      @error('content') aria-describedby="error-content" @enderror>{{old('content')}}</textarea>
            @error('content')
                <span id="error-content" class="field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>


    <fieldset class="box-quiz-answers" id="quiz-answers-fieldset">
        <legend class="quiz-answers-legend">Respuestas del quiz</legend>

        <p class="quiz-answers-help">
            Añade al menos dos respuestas y selecciona con el botón cuál es la correcta.
        </p>

        <span id="quiz-answer-status" class="visually-hidden" role="status" aria-live="polite"></span>

        <div class="quiz-answers-header" aria-hidden="true">
            <span>¿Correcta?</span>
            <span class="text-center">Respuestas</span>
        </div>

        @foreach(range(1,4) as $pos)
            @php
                $isCorrect = (bool) old("answer{$pos}_correct");
            @endphp
            <div class="form-group-answer form-group {{$errors->has('answer' . $pos) ? 'form-group-error' : ''}}">
                <div class="box-answer-check">
                    <button type="button"
                            class="answer-check-btn {{ $isCorrect ? 'is-correct' : '' }}"
                            data-answer-pos="{{ $pos }}"
                            aria-pressed="{{ $isCorrect ? 'true' : 'false' }}"
                            aria-label="Marcar respuesta {{ $pos }} como correcta">
                        <span class="answer-check answer-check-on {{ $isCorrect ? '' : 'hidden' }}" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" aria-hidden="true" focusable="false">
                                <path d="M5 13L9 17L19 7" stroke="#4ade80" stroke-width="2.5" fill="none"/>
                            </svg>
                        </span>

                        <span class="answer-check answer-check-off {{ $isCorrect ? 'hidden' : '' }}" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" aria-hidden="true" focusable="false">
                              <path d="M6 6L18 18" stroke="#f87171" stroke-width="2.5"/>
                              <path d="M6 18L18 6" stroke="#f87171" stroke-width="2.5"/>
                            </svg>
                        </span>
                    </button>
                </div>

                <input class="answer-checkbox hidden" type="checkbox" name="answer{{$pos}}_correct" value="1" {{ $isCorrect ? 'checked' : '' }}>
                <label for="suggestion-answer-{{$pos}}" class="visually-hidden">Respuesta {{$pos}}</label>
                <input id="suggestion-answer-{{$pos}}"
                       type="text"
                       name="answer{{$pos}}"
                       class="form-control"
                       maxlength="255"
                       placeholder="Respuesta {{$pos}}"
                       value="{{old('answer' . $pos)}}"
                       aria-invalid="{{ $errors->has('answer' . $pos) ? 'true' : 'false' }}">
            </div>
        @endforeach

        @error('options')
            <span id="error-options" class="field-error" role="alert">{{ $message }}</span>
        @enderror
    </fieldset>

    @php
        $nonThrottleErrors = collect($errors->all())->filter(function($error) {
            return !Str::contains($error, 'espera');
        });
    @endphp

    @if($nonThrottleErrors->isNotEmpty())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach($nonThrottleErrors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    @if($errors->has('throttle'))
        <div class="alert alert-warning throttle-alert" id="throttleAlert" role="alert">
            <div class="throttle-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="#f5a623" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" class="timer-icon" aria-hidden="true" focusable="false">
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
    <div class="form-terms-field">
        <div class="form-terms-row">
            <input id="terms"
                   type="checkbox"
                   name="terms"
                   value="1"
                   {{ old('terms') ? 'checked' : '' }}
                   required
                   aria-invalid="{{ $errors->has('terms') ? 'true' : 'false' }}"
                   @error('terms') aria-describedby="error-terms" @enderror>
            <label for="terms">
                Confirmo que he leído
                <a href="{{route('page.show', 'normas')}}"
                   title="Enlace a las normas de la comunidad en {{config('app.name')}}" target="_blank" rel="noopener noreferrer">las
                    Normas</a>
                sobre contenido adecuado para la comunidad, acepto
                <a href="{{route('page.show', 'politica-de-privacidad')}}"
                   title="Enlace a la política de privacidad en {{config('app.name')}}" target="_blank" rel="noopener noreferrer">la
                    Política de Privacidad</a>
                y
                <a href="{{route('page.show', 'condiciones-de-uso')}}"
                   title="Enlace a las condiciones de uso en {{config('app.name')}}" target="_blank" rel="noopener noreferrer">las
                    Condiciones de Uso</a> <span aria-hidden="true">*</span>
            </label>
        </div>
        @error('terms')
            <span id="error-terms" class="field-error" role="alert">{{ $message }}</span>
        @enderror
    </div>


    {{-- Google Recaptcha --}}
    @if(config('google.recaptcha.site_key'))
        <div>
            <input type="hidden" name="g_recaptcha" id="g_recaptcha">
        </div>
    @endif

    <button type="submit" class="btn">😜 Enviar 😝</button>
</form>
