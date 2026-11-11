<x-app-layout>
    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-dashboard.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-wizard.css') }}"
    >

    @php
        $selectedExercises = old('exercises', $selected ?? []);

        $categories = [
            'abdomen' => 'Abdomen',
            'pecho' => 'Pecho',
            'espalda' => 'Espalda',
            'biceps' => 'Bíceps',
            'triceps' => 'Tríceps',
            'cuadriceps' => 'Cuádriceps',
            'hombro' => 'Hombro',
            'femoral' => 'Femoral',
            'gemelo' => 'Gemelo',
            'aductor' => 'Aductor',
            'antebrazo' => 'Antebrazo',
        ];
    @endphp

    <div class="tf-dashboard tf-wizard-page">
        <h1 class="tf-title">{{ $dayName }}</h1>

        <p class="tf-subtitle">
            Elige los ejercicios para este día.
        </p>

        <form
            id="routineForm"
            method="POST"
            action="{{ route('rutina.create.postDay', ['day' => $day]) }}"
            class="tf-card tf-wizard-day-card"
        >
            @csrf

            {{-- Acción por defecto si se envía con Enter. --}}
            <input
                type="hidden"
                id="wizardAction"
                name="wizard_action"
                value="next"
            >

            <div class="tf-wizard-actions tf-wizard-actions-top">
                @if (!$isFirst)
                    <button
                        type="submit"
                        class="tf-link"
                        data-wizard-action="previous"
                    >
                        ← Día anterior
                    </button>
                @endif

                <button
                    type="submit"
                    class="tf-btn"
                    data-wizard-action="next"
                >
                    {{ $isLast ? 'Ir al resumen' : 'Siguiente día' }}
                </button>

                <button
                    type="submit"
                    class="tf-link"
                    data-wizard-action="change_days"
                >
                    Cambiar días
                </button>
            </div>

            @if (session('error'))
                <div class="tf-error tf-wizard-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="tf-error tf-wizard-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="tf-filters">
                <label class="tf-wizard-sr-only" for="catFilter">
                    Grupo muscular
                </label>

                <select
                    id="catFilter"
                    class="tf-input tf-input-sm"
                >
                    <option value="all">Todas</option>

                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <label class="tf-wizard-sr-only" for="nameSearch">
                    Buscar ejercicio por nombre
                </label>

                <input
                    id="nameSearch"
                    class="tf-input tf-input-sm"
                    type="search"
                    placeholder="Buscar por nombre…"
                    autocomplete="off"
                >

                <button
                    type="button"
                    id="clearFilters"
                    class="tf-btn tf-btn-small"
                >
                    Limpiar
                </button>
            </div>

            <p
                id="selectionCount"
                class="tf-wizard-note"
                aria-live="polite"
            ></p>

            <div class="tf-ex-grid">
                @foreach ($exercises as $ex)
                    <label
                        class="tf-ex-card"
                        data-group="{{ \Illuminate\Support\Str::slug($ex['group']) }}"
                        data-tags="{{ implode(' ', $ex['tags'] ?? []) }}"
                        data-name="{{ $ex['name'] }}"
                    >
                        <input
                            type="checkbox"
                            name="exercises[]"
                            value="{{ $ex['id'] }}"
                            @checked(
                                in_array(
                                    $ex['id'],
                                    $selectedExercises,
                                    true
                                )
                            )
                        >

                        <img
                            class="ex-img"
                            src="{{ asset($ex['frame1']) }}"
                            data-frame1="{{ asset($ex['frame1']) }}"
                            data-frame2="{{ asset($ex['frame2']) }}"
                            alt="{{ $ex['name'] }}"
                            loading="lazy"
                        >

                        <div class="tf-ex-title">
                            {{ $ex['name'] }}
                        </div>

                        <div class="tf-ex-group">
                            {{ $ex['group'] }}
                        </div>
                    </label>
                @endforeach
            </div>

            <p
                id="noFilterResults"
                class="tf-wizard-note"
                hidden
            >
                No hay ejercicios que coincidan con la búsqueda.
            </p>

            <div class="tf-wizard-actions">
                <button
                    type="submit"
                    class="tf-btn"
                    data-wizard-action="next"
                >
                    {{ $isLast ? 'Ir al resumen' : 'Siguiente día' }}
                </button>

                <button
                    type="submit"
                    class="tf-link"
                    data-wizard-action="change_days"
                >
                    Cambiar días
                </button>
            </div>
        </form>
    </div>

    <div
        id="tf-alert"
        class="tf-alert-overlay"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="tf-alert-text"
    >
        <div class="tf-alert-box">
            <div class="tf-alert-icon" aria-hidden="true">
                ❗
            </div>

            <div id="tf-alert-text" class="tf-alert-text">
                No has seleccionado ningún ejercicio.
            </div>

            <button
                type="button"
                id="tf-alert-close"
                class="tf-btn tf-btn-small"
            >
                Cerrar
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('routineForm');
            const actionInput = document.getElementById('wizardAction');
            const category = document.getElementById('catFilter');
            const search = document.getElementById('nameSearch');
            const clear = document.getElementById('clearFilters');
            const counter = document.getElementById('selectionCount');
            const noResults = document.getElementById('noFilterResults');
            const alertBox = document.getElementById('tf-alert');
            const closeAlert = document.getElementById('tf-alert-close');

            const cards = Array.from(
                form.querySelectorAll('.tf-ex-card')
            );

            const images = form.querySelectorAll('.ex-img');

            let returnFocus = null;

            function normalize(value) {
                return value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .trim();
            }

            function applyFilters() {
                const group = normalize(category.value);
                const query = normalize(search.value);
                let visibleCount = 0;

                cards.forEach(card => {
                    const cardGroup = normalize(card.dataset.group || '');

                    const tags = normalize(card.dataset.tags || '')
                        .split(/\s+/);

                    const name = normalize(card.dataset.name || '');

                    const matchesGroup = group === 'all'
                        || cardGroup === group
                        || tags.includes(group);

                    const matchesName = !query || name.includes(query);

                    const visible = matchesGroup && matchesName;

                    card.style.display = visible ? '' : 'none';

                    if (visible) visibleCount++;
                });

                noResults.hidden = visibleCount > 0;
            }

            function updateCounter() {
                const count = form.querySelectorAll(
                    'input[name="exercises[]"]:checked'
                ).length;

                counter.textContent = count === 1
                    ? '1 ejercicio seleccionado'
                    : `${count} ejercicios seleccionados`;
            }

            function hideAlert() {
                alertBox.style.display = 'none';
                returnFocus?.focus();
            }

            category.addEventListener('change', applyFilters);
            search.addEventListener('input', applyFilters);

            clear.addEventListener('click', () => {
                category.value = 'all';
                search.value = '';
                applyFilters();
                search.focus();
            });

            form.addEventListener('change', event => {
                if (event.target.matches('input[name="exercises[]"]')) {
                    updateCounter();
                }
            });

            // Enter en el buscador filtra, no envía el formulario.
            search.addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    applyFilters();
                }
            });

            form.addEventListener('submit', event => {
                const action = event.submitter
                    ?.dataset.wizardAction || 'next';

                actionInput.value = action;

                const selectedCount = form.querySelectorAll(
                    'input[name="exercises[]"]:checked'
                ).length;

                // Volver y cambiar días permiten guardar un día vacío.
                if (action === 'next' && selectedCount === 0) {
                    event.preventDefault();

                    returnFocus = event.submitter;
                    alertBox.style.display = 'flex';
                    closeAlert.focus();
                }
            });

            closeAlert.addEventListener('click', hideAlert);

            alertBox.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    hideAlert();
                }

                if (event.key === 'Tab') {
                    event.preventDefault();
                    closeAlert.focus();
                }
            });

            const reducedMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            );

            if (images.length) {
                let flip = false;

                window.setInterval(() => {
                    if (document.hidden) return;

                    flip = reducedMotion.matches ? false : !flip;

                    images.forEach(image => {
                        const frame = flip
                            ? image.dataset.frame2
                            : image.dataset.frame1;

                        if (image.getAttribute('src') !== frame) {
                            image.src = frame;
                        }
                    });
                }, 1000);
            }

            applyFilters();
            updateCounter();
        });
    </script>
</x-app-layout>