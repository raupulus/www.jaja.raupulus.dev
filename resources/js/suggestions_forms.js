/**
 * Funcionalidad del formulario de sugerencias
 */

document.addEventListener('DOMContentLoaded', function () {
    initSuggestionsForm();
});

function initSuggestionsForm() {
    initTypeSelector();
    initGroupSelector();
    initAnswerCheckers();
    initImagePreview();
    initThrottleCounter();
    initDefaultCorrectAnswer();
    initAccessibleValidation();
    initSubmitProtection();
}

/**
 * Sincroniza visualmente un botón selector de respuesta correcta de quiz.
 */
function setQuizButtonState(button, isCorrect) {
    const answerGroup = button.closest('.form-group-answer');
    if (!answerGroup) return;

    const checkOn = button.querySelector('.answer-check-on');
    const checkOff = button.querySelector('.answer-check-off');
    const checkbox = answerGroup.querySelector('.answer-checkbox');

    button.setAttribute('aria-pressed', isCorrect ? 'true' : 'false');
    button.classList.toggle('is-correct', isCorrect);

    if (checkOn && checkOff) {
        checkOn.classList.toggle('hidden', !isCorrect);
        checkOff.classList.toggle('hidden', isCorrect);
    }

    if (checkbox) {
        checkbox.checked = isCorrect;
    }
}

/**
 * Selecciona una única respuesta como correcta y anuncia el cambio si corresponde.
 */
function selectCorrectAnswer(targetButton, announce = true) {
    const answerButtons = document.querySelectorAll('.answer-check-btn');
    answerButtons.forEach((btn) => {
        setQuizButtonState(btn, btn === targetButton);
    });

    if (announce) {
        const statusRegion = document.getElementById('quiz-answer-status');
        const pos = targetButton.dataset.answerPos || '';
        if (statusRegion && pos) {
            statusRegion.textContent = `Respuesta ${pos} marcada como correcta.`;
        }
    }
}

/**
 * Selecciona la primera respuesta como correcta al cargar la página si no hay ninguna marcada.
 */
function initDefaultCorrectAnswer() {
    const quizAnswersBlock = document.querySelector('.box-quiz-answers');
    if (!quizAnswersBlock || quizAnswersBlock.hidden || quizAnswersBlock.style.display === 'none') {
        return;
    }

    const alreadyChecked = quizAnswersBlock.querySelector('.answer-checkbox:checked');
    if (alreadyChecked) {
        const existingBtn = alreadyChecked.closest('.form-group-answer')?.querySelector('.answer-check-btn');
        if (existingBtn) {
            selectCorrectAnswer(existingBtn, false);
        }
        return;
    }

    const firstButton = quizAnswersBlock.querySelector('.answer-check-btn');
    if (firstButton) {
        selectCorrectAnswer(firstButton, false);
    }
}

/**
 * Maneja la visibilidad del bloque de respuestas según el tipo seleccionado.
 */
function initTypeSelector() {
    const typeSelect = document.querySelector('select[name="type_id"]');
    const quizAnswersBlock = document.querySelector('.box-quiz-answers');

    if (!typeSelect || !quizAnswersBlock) return;

    function toggleQuizAnswers() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const slug = selectedOption ? selectedOption.getAttribute('data-slug') : '';

        if (slug === 'quiz') {
            quizAnswersBlock.hidden = false;
            quizAnswersBlock.style.display = 'flex';
            initDefaultCorrectAnswer();
        } else {
            quizAnswersBlock.hidden = true;
            quizAnswersBlock.style.display = 'none';
            clearAllAnswers();
        }
    }

    toggleQuizAnswers();
    typeSelect.addEventListener('change', toggleQuizAnswers);
}

/**
 * Maneja el filtrado de grupos según el tipo seleccionado.
 */
function initGroupSelector() {
    const typeSelect = document.querySelector('select[name="type_id"]');
    const groupSelect = document.querySelector('select[name="group_id"]');

    if (!typeSelect || !groupSelect) return;

    function filterGroups(resetSelection = true) {
        const selectedTypeId = typeSelect.value;
        const groupOptions = groupSelect.querySelectorAll('option');

        if (resetSelection) {
            groupSelect.value = '';
        } else if (groupSelect.value) {
            const currentSelected = groupSelect.options[groupSelect.selectedIndex];
            if (currentSelected && currentSelected.getAttribute('data-type_id') !== selectedTypeId) {
                groupSelect.value = '';
            }
        }

        groupOptions.forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                option.style.display = '';
                return;
            }

            const matchesType = selectedTypeId === option.getAttribute('data-type_id');
            option.hidden = !matchesType;
            option.style.display = matchesType ? '' : 'none';
        });
    }

    function handleInitialType() {
        let selectedTypeId = typeSelect.value;

        if (!selectedTypeId) {
            const firstOption = typeSelect.querySelector('option[value]:not([value=""])');
            if (firstOption) {
                selectedTypeId = firstOption.value;
                typeSelect.value = selectedTypeId;
            }
        }

        filterGroups(false);
    }

    handleInitialType();
    typeSelect.addEventListener('change', () => filterGroups(true));
}

/**
 * Maneja la funcionalidad accesible de selección de respuestas correctas (ratón y teclado).
 */
function initAnswerCheckers() {
    const answerButtons = document.querySelectorAll('.answer-check-btn');

    answerButtons.forEach((button) => {
        button.addEventListener('click', function () {
            if (button.getAttribute('aria-pressed') === 'true') {
                return;
            }

            selectCorrectAnswer(button, true);
        });
    });
}

/**
 * Limpia todas las respuestas seleccionadas.
 */
function clearAllAnswers() {
    const answerButtons = document.querySelectorAll('.answer-check-btn');
    answerButtons.forEach((btn) => setQuizButtonState(btn, false));

    const statusRegion = document.getElementById('quiz-answer-status');
    if (statusRegion) {
        statusRegion.textContent = '';
    }
}

/**
 * Maneja la previsualización de imágenes (activada nativamente por <label for="content-image">).
 */
function initImagePreview() {
    const imageInput = document.getElementById('content-image');
    if (!imageInput) return;

    imageInput.addEventListener('change', handleImagePreview);
}

/**
 * Maneja la vista previa y validación de tamaño en cliente de la imagen seleccionada.
 */
function handleImagePreview(e) {
    const input = e.target;
    const file = input.files[0];
    const errorElement = document.getElementById('file-error');
    const previewImg = document.getElementById('preview-img');
    const placeholder = document.getElementById('preview-placeholder');

    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }

    input.setAttribute('aria-invalid', 'false');
    if (input.getAttribute('aria-describedby') === 'file-error') {
        input.removeAttribute('aria-describedby');
    }

    if (file) {
        if (file.size > 2097152) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
            if (errorElement) {
                errorElement.textContent = `La imagen es demasiado grande (${fileSizeMB}MB). Máximo permitido: 2MB.`;
                errorElement.style.display = 'block';
            }
            input.setAttribute('aria-invalid', 'true');
            input.setAttribute('aria-describedby', 'file-error');
            input.value = '';

            if (placeholder) placeholder.style.display = 'flex';
            if (previewImg) {
                previewImg.removeAttribute('src');
                previewImg.classList.add('hidden');
                previewImg.style.display = 'none';
            }
            return false;
        }

        const reader = new FileReader();
        reader.onload = function (loadEvent) {
            if (previewImg) {
                previewImg.src = loadEvent.target.result;
                previewImg.classList.remove('hidden');
                previewImg.style.display = 'block';
            }
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    } else {
        if (placeholder) placeholder.style.display = 'flex';
        if (previewImg) {
            previewImg.removeAttribute('src');
            previewImg.classList.add('hidden');
            previewImg.style.display = 'none';
        }
    }

    return true;
}

/**
 * Sincroniza aria-invalid con :user-invalid tras la interacción sin pisar errores iniciales de servidor.
 */
function initAccessibleValidation() {
    const form = document.getElementById('form-suggestion-send');
    if (!form) return;

    const serverInvalid = new WeakSet();
    form.querySelectorAll('input, select, textarea').forEach((el) => {
        if (el.getAttribute('aria-invalid') === 'true') {
            serverInvalid.add(el);
        }
    });

    const syncAria = (el) => {
        if (!el || !el.matches?.('input, select, textarea')) return;
        let isUserInvalid = false;
        try {
            isUserInvalid = el.matches(':user-invalid');
        } catch {
            isUserInvalid = !el.validity.valid;
        }

        if (isUserInvalid) {
            el.setAttribute('aria-invalid', 'true');
            serverInvalid.delete(el);
        } else if (!serverInvalid.has(el)) {
            el.setAttribute('aria-invalid', 'false');
        }
    };

    form.addEventListener('blur', (event) => syncAria(event.target), true);
    form.addEventListener('change', (event) => {
        serverInvalid.delete(event.target);
        syncAria(event.target);
    }, true);
    form.addEventListener('input', (event) => {
        if (event.target.getAttribute('aria-invalid') === 'true') {
            serverInvalid.delete(event.target);
            syncAria(event.target);
        }
    });
    form.addEventListener('invalid', (event) => syncAria(event.target), true);
}

/**
 * Evita el doble envío del formulario mostrando estado de carga.
 */
function initSubmitProtection() {
    const form = document.getElementById('form-suggestion-send');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = 'true';
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('disabled');
            submitButton.textContent = 'Enviando...';
        }
    });
}

/**
 * Maneja el contador de throttle.
 */
function initThrottleCounter() {
    const throttleCounter = document.getElementById('throttleCounter');
    if (!throttleCounter) return;

    let seconds = parseInt(throttleCounter.dataset.seconds, 10);
    if (isNaN(seconds) || seconds <= 0) return;

    const submitButton = document.querySelector('#form-suggestion-send button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.classList.add('disabled');
    }

    const countdownInterval = setInterval(function () {
        seconds--;
        throttleCounter.textContent = seconds;

        if (seconds <= 0) {
            clearInterval(countdownInterval);

            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('disabled');
            }

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

/**
 * Desplaza y enfoca el formulario cuando hay errores de validación.
 */
function goToForm() {
    const form = document.getElementById('form-suggestion-send');
    if (!form) return;

    const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    form.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth' });

    const firstInvalid = form.querySelector('[aria-invalid="true"]');
    if (firstInvalid && typeof firstInvalid.focus === 'function') {
        firstInvalid.focus({ preventScroll: true });
    }
}

window.goToForm = goToForm;
