/**
 * Funcionalidad del formulario de sugerencias
 */

document.addEventListener('DOMContentLoaded', function () {
    initSuggestionsForm();
});

function initSuggestionsForm() {
    // Inicializar funcionalidades
    initTypeSelector();
    initGroupSelector();
    initAnswerCheckers();
    initImagePreview();
    initThrottleCounter();
    initDefaultCorrectAnswer();
}

/**
 * Selecciona la primera respuesta como correcta al cargar la página
 */
function initDefaultCorrectAnswer() {
    // Solo ejecutar si el bloque de quiz está visible
    const quizAnswersBlock = document.querySelector('.box-quiz-answers');
    if (!quizAnswersBlock || quizAnswersBlock.style.display === 'none') return;

    const firstAnswerBox = document.querySelector('.box-answer-check');
    if (firstAnswerBox) {
        const checkOn = firstAnswerBox.querySelector('.answer-check-on');
        const checkOff = firstAnswerBox.querySelector('.answer-check-off');
        const checkbox = firstAnswerBox.closest('.form-group-answer').querySelector('.answer-checkbox');

        if (checkOn && checkOff && checkbox) {
            // Marcar la primera como correcta sin animación (carga inicial)
            checkOn.classList.remove('hidden');
            checkOff.classList.add('hidden');
            checkbox.checked = true;
        }
    }
}

/**
 * Maneja la visibilidad del bloque de respuestas según el tipo seleccionado
 */
function initTypeSelector() {
    const typeSelect = document.querySelector('select[name="type_id"]');
    const quizAnswersBlock = document.querySelector('.box-quiz-answers');

    if (!typeSelect || !quizAnswersBlock) return;

    // Función para mostrar/ocultar el bloque de respuestas
    function toggleQuizAnswers() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const slug = selectedOption.getAttribute('data-slug');

        if (slug === 'quiz') {
            quizAnswersBlock.style.display = 'block';
            // Marcar la primera respuesta como correcta cuando se muestra el bloque
            setTimeout(initDefaultCorrectAnswer, 100);
        } else {
            quizAnswersBlock.style.display = 'none';
            // Limpiar todas las respuestas cuando se oculta
            clearAllAnswers();
        }
    }

    // Ejecutar al cargar la página
    toggleQuizAnswers();

    // Ejecutar cuando cambie la selección
    typeSelect.addEventListener('change', toggleQuizAnswers);
}

/**
 * Maneja el filtrado de grupos según el tipo seleccionado
 */
function initGroupSelector() {
    const typeSelect = document.querySelector('select[name="type_id"]');
    const groupSelect = document.querySelector('select[name="group_id"]');

    if (!typeSelect || !groupSelect) return;

    // Función para filtrar grupos según el tipo
    function filterGroups() {
        const selectedTypeId = typeSelect.value;
        const groupOptions = groupSelect.querySelectorAll('option');

        // Resetear selección de grupo
        groupSelect.value = '';

        // Filtrar opciones de grupo
        groupOptions.forEach(option => {
            if (option.value === '') {
                // Mantener la opción por defecto "Grupo"
                option.style.display = 'block';
                return;
            }

            const optionTypeId = option.getAttribute('data-type_id');

            if (selectedTypeId === optionTypeId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }

    // Función para manejar el tipo seleccionado al cargar
    function handleInitialType() {
        let selectedTypeId = typeSelect.value;

        // Si no hay tipo seleccionado, seleccionar el primero
        if (!selectedTypeId) {
            const firstOption = typeSelect.querySelector('option[value]:not([value=""])');
            if (firstOption) {
                selectedTypeId = firstOption.value;
                typeSelect.value = selectedTypeId;
            }
        }

        // Aplicar filtro de grupos
        filterGroups();
    }

    // Ejecutar al cargar la página
    handleInitialType();

    // Ejecutar cuando cambie el tipo
    typeSelect.addEventListener('change', filterGroups);
}

/**
 * Maneja la funcionalidad de selección de respuestas correctas
 */
function initAnswerCheckers() {
    const answerBoxes = document.querySelectorAll('.box-answer-check');

    answerBoxes.forEach(box => {
        const checkOn = box.querySelector('.answer-check-on');
        const checkOff = box.querySelector('.answer-check-off');
        const checkbox = box.closest('.form-group-answer').querySelector('.answer-checkbox');

        if (!checkOn || !checkOff || !checkbox) return;

        // Solo permitir hacer clic en la cruz (checkOff) para activar
        checkOff.addEventListener('click', function() {
            // Desmarcar todas las demás respuestas primero con animación
            clearAllAnswersWithAnimation();

            // Esperar un poco para que termine la animación de limpieza
            setTimeout(() => {
                // Marcar esta respuesta como correcta con animación
                setCorrectAnswerWithAnimation(checkOn, checkOff, checkbox);
            }, 150);
        });

        // No permitir hacer clic en el check verde para desmarcarlo
        checkOn.addEventListener('click', function(e) {
            e.preventDefault();
            // Añadir una pequeña animación de "rebote" para indicar que no se puede desmarcar
            checkOn.style.transform = 'scale(1.1)';
            setTimeout(() => {
                checkOn.style.transform = 'scale(1)';
            }, 150);
        });
    });
}

/**
 * Marca una respuesta como correcta con animación
 */
function setCorrectAnswerWithAnimation(checkOn, checkOff, checkbox) {
    // Ocultar cruz con animación
    checkOff.style.transform = 'scale(0.8)';
    checkOff.style.opacity = '0';

    setTimeout(() => {
        checkOff.classList.add('hidden');
        checkOff.style.transform = 'scale(1)';
        checkOff.style.opacity = '1';

        // Mostrar check con animación
        checkOn.classList.remove('hidden');
        checkOn.style.transform = 'scale(0.8)';
        checkOn.style.opacity = '0';

        // Animar la aparición del check
        setTimeout(() => {
            checkOn.style.transform = 'scale(1.1)';
            checkOn.style.opacity = '1';

            // Rebote final
            setTimeout(() => {
                checkOn.style.transform = 'scale(1)';
            }, 150);
        }, 50);

        checkbox.checked = true;
    }, 150);
}

/**
 * Limpia todas las respuestas con animación
 */
function clearAllAnswersWithAnimation() {
    const answerBoxes = document.querySelectorAll('.box-answer-check');

    answerBoxes.forEach(box => {
        const checkOn = box.querySelector('.answer-check-on');
        const checkOff = box.querySelector('.answer-check-off');
        const checkbox = box.closest('.form-group-answer').querySelector('.answer-checkbox');

        if (checkOn && checkOff && checkbox && !checkOn.classList.contains('hidden')) {
            // Animar la desaparición del check
            checkOn.style.transform = 'scale(0.8)';
            checkOn.style.opacity = '0';

            setTimeout(() => {
                checkOn.classList.add('hidden');
                checkOn.style.transform = 'scale(1)';
                checkOn.style.opacity = '1';

                // Mostrar cruz con animación
                checkOff.classList.remove('hidden');
                checkOff.style.transform = 'scale(0.8)';
                checkOff.style.opacity = '0';

                setTimeout(() => {
                    checkOff.style.transform = 'scale(1)';
                    checkOff.style.opacity = '1';
                }, 50);

                checkbox.checked = false;
            }, 150);
        }
    });
}

/**
 * Limpia todas las respuestas (sin animación - para uso interno)
 */
function clearAllAnswers() {
    const answerBoxes = document.querySelectorAll('.box-answer-check');

    answerBoxes.forEach(box => {
        const checkOn = box.querySelector('.answer-check-on');
        const checkOff = box.querySelector('.answer-check-off');
        const checkbox = box.closest('.form-group-answer').querySelector('.answer-checkbox');

        if (checkOn && checkOff && checkbox) {
            checkOn.classList.add('hidden');
            checkOff.classList.remove('hidden');
            checkbox.checked = false;
        }
    });
}

/**
 * Maneja la previsualización de imágenes
 */
function initImagePreview() {
    const imageInput = document.getElementById('content-image');
    const previewContainer = document.getElementById('image-preview-container');

    if (!imageInput) return;

    // Evento para cambio de archivo
    imageInput.addEventListener('change', handleImagePreview);

    // Evento para hacer clic en el contenedor de vista previa
    if (previewContainer) {
        previewContainer.addEventListener('click', () => {
            imageInput.click();
        });
    }
}

/**
 * Maneja la vista previa de la imagen seleccionada
 */
function handleImagePreview(e) {
    const input = e.target;
    const file = input.files[0];
    const errorElement = document.getElementById('file-error');
    const previewImg = document.getElementById('preview-img');
    const placeholder = document.getElementById('preview-placeholder');

    // Limpiar errores previos
    if (errorElement) {
        errorElement.style.display = 'none';
    }

    if (file) {
        // Validar tamaño (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
            if (errorElement) {
                errorElement.textContent = `La imagen es demasiado grande (${fileSizeMB}MB). Máximo permitido: 2MB`;
                errorElement.style.display = 'block';
            }
            input.value = '';

            // Mostrar placeholder, ocultar imagen
            if (placeholder) placeholder.style.display = 'flex';
            if (previewImg) previewImg.style.display = 'none';
            return false;
        }

        // Mostrar preview
        const reader = new FileReader();
        reader.onload = function (e) {
            if (previewImg) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            }
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    } else {
        // Si no hay archivo, mostrar placeholder
        if (placeholder) placeholder.style.display = 'flex';
        if (previewImg) previewImg.style.display = 'none';
    }

    return true;
}

/**
 * Maneja el contador de throttle
 */
function initThrottleCounter() {
    const throttleCounter = document.getElementById('throttleCounter');

    if (!throttleCounter) return;

    let seconds = parseInt(throttleCounter.dataset.seconds, 10);

    if (isNaN(seconds) || seconds <= 0) return;

    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.classList.add('disabled');
    }

    // Iniciar contador
    const countdownInterval = setInterval(function () {
        seconds--;
        throttleCounter.textContent = seconds;

        // Cuando llega a cero
        if (seconds <= 0) {
            clearInterval(countdownInterval);

            // Habilitar botón de envío
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('disabled');
            }

            // Ocultar el mensaje de throttle con animación
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
 * Función para hacer scroll al formulario (útil cuando hay errores)
 */
function goToForm() {
    const form = document.getElementById('form-suggestion-send');
    if (form) {
        form.scrollIntoView({behavior: 'smooth'});
    }
}

// Exportar función para uso global si es necesario
window.goToForm = goToForm;
