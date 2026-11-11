<x-app-layout>
    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-dashboard.css') }}"
    >

    @php
        $dayNames = [
            'mon' => 'Lunes',
            'tue' => 'Martes',
            'wed' => 'Miércoles',
            'thu' => 'Jueves',
            'fri' => 'Viernes',
            'sat' => 'Sábado',
            'sun' => 'Domingo',
        ];

        $routineName = $routineDay->routine->name;
        $dayLabel = $dayNames[$routineDay->day] ?? $routineDay->day;
        $exerciseCount = $routineDay->exercises->count();

        $isActive = $workout
            && $workout->started_at
            && ! $workout->ended_at;

        $intensities = [
            'light' => [
                'label' => 'Suave',
                'description' => 'Sesión tranquila o con poco esfuerzo.',
            ],
            'moderate' => [
                'label' => 'Normal',
                'description' => 'Entrenamiento habitual con esfuerzo moderado.',
            ],
            'intense' => [
                'label' => 'Intenso',
                'description' => 'Sesión exigente y de alta intensidad.',
            ],
        ];

        $restOptions = [
            30 => [
                'label' => '< 1 min',
                'description' => 'Usaremos 30 segundos.',
            ],
            60 => [
                'label' => '1 min',
                'description' => 'Descanso corto.',
            ],
            120 => [
                'label' => '2 min',
                'description' => 'Descanso moderado.',
            ],
            180 => [
                'label' => '3 min',
                'description' => 'Descanso largo.',
            ],
            300 => [
                'label' => '> 3 min',
                'description' => 'Usaremos 5 minutos.',
            ],
        ];

        $restoringInput = old('workout_id') !== null;

        $previousSets = $restoringInput
            ? collect(old('sets', []))
            : collect($workout?->sets ?? []);

        $globalIndex = 0;
    @endphp

    <style>
        /* Estilos exclusivos de esta pantalla. */
        .tf-workout-page,
        .tf-workout-page *,
        .tf-workout-page *::before,
        .tf-workout-page *::after {
            box-sizing: border-box;
        }

        .tf-dashboard.tf-workout-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            min-height: 100vh;
            margin: 0;
            padding: 36px 20px 60px;
            background: #000;
            color: #fff;
            text-align: left;
        }

        .tf-workout-page .wp-header,
        .tf-workout-page .wp-card {
            width: 100%;
            max-width: 980px;
            min-width: 0;
            margin-left: auto;
            margin-right: auto;
        }

        .tf-workout-page .wp-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
            text-align: center;
        }

        .tf-workout-page .wp-kicker {
            color: #c4a2ee;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .tf-workout-page .wp-title {
            max-width: 100%;
            margin: 0;
            padding: 16px 36px;
            border: 2px solid #7c3aed;
            border-radius: 999px;
            color: #fff;
            font-size: clamp(1.8rem, 4vw, 3rem);
            font-weight: 900;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .tf-workout-page .wp-meta {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tf-workout-page .wp-meta span {
            padding: 8px 12px;
            border: 1px solid rgba(124, 58, 237, .4);
            border-radius: 10px;
            background: #170e22;
            color: #e3d3f1;
            font-size: 13px;
            line-height: 1.5;
        }

        .tf-workout-page .wp-subtitle {
            max-width: 650px;
            margin: 0;
            color: #bfb5c7;
            font-size: 14px;
            line-height: 1.7;
        }

        .tf-workout-page .wp-card {
            padding: 28px;
            border: 2px solid #292929;
            border-radius: 22px;
            background: #090909;
        }

        .tf-workout-page .wp-card form {
            min-width: 0;
            margin: 0;
        }

        .tf-workout-page .wp-exercise {
            min-width: 0;
            margin: 0 0 18px;
            padding: 20px;
            border: 1px solid #272727;
            border-radius: 18px;
            background: #0e0e0e;
        }

        .tf-workout-page .wp-exercise-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .tf-workout-page .wp-exercise-head > div {
            min-width: 0;
        }

        .tf-workout-page .wp-exercise-title {
            margin: 0 0 5px;
            color: #fff;
            font-size: 17px;
            font-weight: 800;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .tf-workout-page .wp-description {
            margin: 0;
            color: #b5abbc;
            font-size: 13px;
            line-height: 1.6;
        }

        .tf-workout-page .wp-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            gap: 8px;
            min-height: 44px;
            margin: 0;
            padding: 10px 14px;
            border: 1px solid #7742b4;
            border-radius: 11px;
            background: transparent;
            color: #fff;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.4;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        .tf-workout-page .wp-button:hover {
            border-color: #b28be4;
            background: #21122f;
        }

        .tf-workout-page .wp-button:focus-visible,
        .tf-workout-page .wp-input:focus-visible {
            outline: 2px solid #c4b5fd;
            outline-offset: 3px;
        }

        .tf-workout-page .wp-set-labels,
        .tf-workout-page .wp-set-row {
            display: grid;
            grid-template-columns:
                46px
                minmax(0, 1fr)
                minmax(0, 1fr)
                88px;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .tf-workout-page .wp-set-labels {
            margin-bottom: 9px;
            color: #aca1b5;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .tf-workout-page .wp-set-row {
            margin-bottom: 12px;
        }

        .tf-workout-page .wp-set-row:last-child {
            margin-bottom: 0;
        }

        .tf-workout-page .wp-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 44px;
            border: 1px solid #543079;
            border-radius: 12px;
            background: #1a1025;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }

        .tf-workout-page .wp-input {
            display: block;
            width: 100%;
            max-width: none;
            min-width: 0;
            height: 46px;
            margin: 0;
            padding: 11px 13px;
            border: 1px solid #38323c;
            border-radius: 12px;
            background: #101010;
            color: #fff;
            font: inherit;
            font-size: 14px;
        }

        .tf-workout-page .wp-input::placeholder {
            color: #958a9e;
        }

        .tf-workout-page .wp-weight {
            position: relative;
            min-width: 0;
        }

        .tf-workout-page .wp-weight .wp-input {
            padding-right: 40px;
        }

        .tf-workout-page .wp-unit {
            position: absolute;
            top: 50%;
            right: 13px;
            transform: translateY(-50%);
            color: #aa9eb3;
            font-size: 12px;
            pointer-events: none;
        }

        .tf-workout-page .wp-finish {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #303030;
        }

        .tf-workout-page .wp-section-title {
            margin: 0 0 8px;
            color: #fff;
            font-size: 20px;
            font-weight: 800;
        }

        .tf-workout-page .wp-fieldset {
            min-width: 0;
            margin: 24px 0 0;
            padding: 0;
            border: 0;
        }

        .tf-workout-page .wp-legend {
            margin-bottom: 10px;
            color: #e1d6e9;
            font-size: 14px;
            font-weight: 700;
        }

        .tf-workout-page .wp-options {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .tf-workout-page .wp-rest-options {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .tf-workout-page .wp-option {
            position: relative;
            min-width: 0;
            cursor: pointer;
        }

        .tf-workout-page .wp-radio {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: 0;
            opacity: 0;
        }

        .tf-workout-page .wp-option-content {
            display: flex;
            flex-direction: column;
            gap: 7px;
            height: 100%;
            padding: 16px 13px;
            border: 1px solid #39313f;
            border-radius: 14px;
            background: #101010;
        }

        .tf-workout-page .wp-option-content strong {
            color: #fff;
            font-size: 14px;
        }

        .tf-workout-page .wp-option-content small {
            color: #b7a9c2;
            font-size: 12px;
            line-height: 1.5;
        }

        .tf-workout-page .wp-radio:checked + .wp-option-content {
            border-color: #9860e4;
            background: #21132f;
            box-shadow: 0 0 0 1px #9860e4;
        }

        .tf-workout-page .wp-radio:focus-visible + .wp-option-content {
            outline: 2px solid #c4b5fd;
            outline-offset: 3px;
        }

        .tf-workout-page .wp-primary {
            margin-top: 24px;
            border-color: #7c3aed;
            background: #7c3aed;
        }

        .tf-workout-page .wp-primary:hover {
            background: #6d28d9;
        }

        .tf-workout-page .wp-errors {
            margin-bottom: 20px;
            padding: 14px 16px;
            border: 1px solid #9d4141;
            border-radius: 12px;
            background: #291313;
            color: #ffd0d0;
            font-size: 14px;
        }

        .tf-workout-page .wp-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .tf-workout-page .wp-back {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }

        .tf-workout-page .wp-empty {
            padding: 18px 0;
        }

        .tf-workout-page .wp-sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip-path: inset(50%);
            white-space: nowrap;
        }

        @media (max-width: 760px) {
            .tf-dashboard.tf-workout-page {
                padding: 26px 14px 44px;
            }

            .tf-workout-page .wp-card {
                padding: 18px 12px;
            }

            .tf-workout-page .wp-exercise {
                padding: 16px 12px;
            }

            .tf-workout-page .wp-rest-options {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 620px) {
            .tf-workout-page .wp-exercise-head {
                flex-direction: column;
            }

            .tf-workout-page .wp-add-set {
                width: 100%;
            }

            .tf-workout-page .wp-set-labels {
                display: none;
            }

            .tf-workout-page .wp-set-row {
                grid-template-columns:
                    40px
                    minmax(0, 1fr)
                    minmax(0, 1fr);
                gap: 8px;
                padding-bottom: 12px;
                border-bottom: 1px solid #252525;
            }

            .tf-workout-page .wp-remove-set {
                grid-column: 2 / -1;
                width: 100%;
            }

            .tf-workout-page .wp-input {
                font-size: 16px;
            }

            .tf-workout-page .wp-options {
                grid-template-columns: minmax(0, 1fr);
            }

            .tf-workout-page .wp-primary,
            .tf-workout-page .wp-back .wp-button {
                width: 100%;
            }

            .tf-workout-page .wp-title {
                padding: 14px 24px;
            }
        }

        @media (max-width: 380px) {
            .tf-workout-page .wp-set-row {
                grid-template-columns: 40px minmax(0, 1fr);
            }

            .tf-workout-page .wp-set-row > .wp-input,
            .tf-workout-page .wp-weight,
            .tf-workout-page .wp-remove-set {
                grid-column: 2;
            }
        }
    </style>

    <div class="tf-dashboard tf-workout-page">
        <header class="wp-header">
            <span class="wp-kicker">
                {{ $isActive
                    ? 'Entrenamiento en curso'
                    : 'Detalle del entrenamiento' }}
            </span>

            <h1 class="wp-title">
                {{ $routineName }}
            </h1>

            <div class="wp-meta">
                <span>
                    Día de la rutina: {{ $dayLabel }}
                </span>

                <span>
                    {{ $exerciseCount }}
                    {{ $exerciseCount === 1
                        ? 'ejercicio programado'
                        : 'ejercicios programados' }}
                </span>
            </div>

            <p class="wp-subtitle">
                Registra las series, repeticiones y peso
                de cada ejercicio.
            </p>
        </header>

        <div class="wp-card">
            @if ($errors->any())
                <div class="wp-errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($isActive)
                <form
                    method="POST"
                    action="{{ route('workout.finish', $routineDay->id) }}"
                    id="workoutForm"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="workout_id"
                        value="{{ $workout->id }}"
                    >

                    @foreach ($routineDay->exercises as $ex)
                        @php
                            $label = $names[$ex->exercise_id]
                                ?? $ex->exercise_id;

                            $exerciseSets = $previousSets
                                ->where('exercise_id', $ex->exercise_id)
                                ->sortBy('set_no')
                                ->values();

                            if (
                                ! $restoringInput
                                && $exerciseSets->isEmpty()
                            ) {
                                $exerciseSets = collect([
                                    ['reps' => null, 'weight' => null],
                                    ['reps' => null, 'weight' => null],
                                    ['reps' => null, 'weight' => null],
                                ]);
                            }
                        @endphp

                        <section
                            class="wp-exercise"
                            data-ex="{{ $ex->exercise_id }}"
                            data-label="{{ $label }}"
                        >
                            <div class="wp-exercise-head">
                                <div>
                                    <h2 class="wp-exercise-title">
                                        {{ $label }}
                                    </h2>

                                    <p class="wp-description">
                                        Introduce las repeticiones y el peso
                                        utilizado en cada serie.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="wp-button wp-add-set"
                                >
                                    + Añadir serie
                                </button>
                            </div>

                            <div
                                class="wp-set-labels"
                                aria-hidden="true"
                            >
                                <span>Serie</span>
                                <span>Repeticiones</span>
                                <span>Peso</span>
                                <span></span>
                            </div>

                            <div class="wp-sets">
                                @foreach ($exerciseSets as $set)
                                    <div class="wp-set-row">
                                        <input
                                            type="hidden"
                                            data-field="exercise_id"
                                            name="sets[{{ $globalIndex }}][exercise_id]"
                                            value="{{ $ex->exercise_id }}"
                                        >

                                        <input
                                            type="hidden"
                                            data-field="set_no"
                                            name="sets[{{ $globalIndex }}][set_no]"
                                            value="{{ $loop->iteration }}"
                                        >

                                        <div
                                            class="wp-badge"
                                            aria-label="Serie {{ $loop->iteration }}"
                                        >
                                            S{{ $loop->iteration }}
                                        </div>

                                        <input
                                            class="wp-input"
                                            data-field="reps"
                                            name="sets[{{ $globalIndex }}][reps]"
                                            type="number"
                                            min="0"
                                            max="200"
                                            inputmode="numeric"
                                            placeholder="Reps: 10"
                                            value="{{ data_get($set, 'reps') }}"
                                            aria-label="Repeticiones de {{ $label }}, serie {{ $loop->iteration }}"
                                        >

                                        <div class="wp-weight">
                                            <input
                                                class="wp-input"
                                                data-field="weight"
                                                name="sets[{{ $globalIndex }}][weight]"
                                                type="number"
                                                min="0"
                                                max="999.99"
                                                step="0.01"
                                                inputmode="decimal"
                                                placeholder="Peso: 60"
                                                value="{{ data_get($set, 'weight') }}"
                                                aria-label="Peso en kilogramos de {{ $label }}, serie {{ $loop->iteration }}"
                                            >

                                            <span class="wp-unit">
                                                kg
                                            </span>
                                        </div>

                                        <button
                                            type="button"
                                            class="wp-button wp-remove-set"
                                            aria-label="Eliminar serie {{ $loop->iteration }} de {{ $label }}"
                                        >
                                            Quitar
                                        </button>
                                    </div>

                                    @php $globalIndex++; @endphp
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <section class="wp-finish">
                        <h2 class="wp-section-title">
                            Finalizar entrenamiento
                        </h2>

                        <p class="wp-description">
                            Indica la intensidad y tu descanso habitual
                            entre series. TrackFit utilizará estos datos
                            junto con las series realizadas y tu peso
                            para estimar el gasto energético.
                        </p>

                        <fieldset class="wp-fieldset">
                            <legend class="wp-legend">
                                Intensidad del entrenamiento
                            </legend>

                            <div class="wp-options">
                                @foreach ($intensities as $value => $option)
                                    <label class="wp-option">
                                        <input
                                            class="wp-radio"
                                            type="radio"
                                            name="intensity"
                                            value="{{ $value }}"
                                            @checked(
                                                old('intensity', 'moderate')
                                                === $value
                                            )
                                            required
                                        >

                                        <span class="wp-option-content">
                                            <strong>
                                                {{ $option['label'] }}
                                            </strong>

                                            <small>
                                                {{ $option['description'] }}
                                            </small>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset class="wp-fieldset">
                            <legend class="wp-legend">
                                Descanso habitual entre series
                            </legend>

                            <div class="wp-options wp-rest-options">
                                @foreach ($restOptions as $value => $option)
                                    <label class="wp-option">
                                        <input
                                            class="wp-radio"
                                            type="radio"
                                            name="rest_seconds"
                                            value="{{ $value }}"
                                            @checked(
                                                (int) old('rest_seconds', 120)
                                                === $value
                                            )
                                            required
                                        >

                                        <span class="wp-option-content">
                                            <strong>
                                                {{ $option['label'] }}
                                            </strong>

                                            <small>
                                                {{ $option['description'] }}
                                            </small>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <button
                            class="wp-button wp-primary"
                            type="submit"
                        >
                            Finalizar y guardar entreno
                        </button>
                    </section>
                </form>
            @else
                <div class="wp-empty">
                    <h2 class="wp-section-title">
                        No hay ningún entreno activo.
                    </h2>

                    <p class="wp-description">
                        Vuelve a esta rutina y selecciona
                        el día que quieres entrenar.
                    </p>
                </div>
            @endif

            <div class="wp-back">
                <a
                    class="wp-button"
                    href="{{ route('rutina.follow', [
                        'routine' => $routineDay->routine_id,
                    ]) }}"
                >
                    ← Volver a esta rutina
                </a>
            </div>

            <p
                id="setMessage"
                class="wp-description"
                role="status"
                aria-live="polite"
            ></p>
        </div>
    </div>

    {{-- Plantilla para añadir series sin interpolar datos en HTML. --}}
    <template id="setTemplate">
        <div class="wp-set-row">
            <input type="hidden" data-field="exercise_id">
            <input type="hidden" data-field="set_no">

            <div class="wp-badge">S?</div>

            <input
                class="wp-input"
                data-field="reps"
                type="number"
                min="0"
                max="200"
                inputmode="numeric"
                placeholder="Reps: 10"
            >

            <div class="wp-weight">
                <input
                    class="wp-input"
                    data-field="weight"
                    type="number"
                    min="0"
                    max="999.99"
                    step="0.01"
                    inputmode="decimal"
                    placeholder="Peso: 60"
                >

                <span class="wp-unit">kg</span>
            </div>

            <button
                type="button"
                class="wp-button wp-remove-set"
            >
                Quitar
            </button>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('workoutForm');
            const template = document.getElementById('setTemplate');
            const message = document.getElementById('setMessage');

            if (!form || !template) return;

            function renumberSets() {
                let globalIndex = 0;

                form.querySelectorAll('.wp-exercise').forEach(block => {
                    const label = block.dataset.label;

                    block.querySelectorAll('.wp-set-row')
                        .forEach((row, position) => {
                            const setNumber = position + 1;
                            const badge = row.querySelector('.wp-badge');

                            badge.textContent = `S${setNumber}`;
                            badge.setAttribute(
                                'aria-label',
                                `Serie ${setNumber}`
                            );

                            row.querySelectorAll('[data-field]')
                                .forEach(input => {
                                    const field = input.dataset.field;

                                    input.name =
                                        `sets[${globalIndex}][${field}]`;

                                    if (field === 'exercise_id') {
                                        input.value = block.dataset.ex;
                                    }

                                    if (field === 'set_no') {
                                        input.value = setNumber;
                                    }

                                    if (field === 'reps') {
                                        input.setAttribute(
                                            'aria-label',
                                            `Repeticiones de ${label}, serie ${setNumber}`
                                        );
                                    }

                                    if (field === 'weight') {
                                        input.setAttribute(
                                            'aria-label',
                                            `Peso en kilogramos de ${label}, serie ${setNumber}`
                                        );
                                    }
                                });

                            row.querySelector('.wp-remove-set')
                                .setAttribute(
                                    'aria-label',
                                    `Eliminar serie ${setNumber} de ${label}`
                                );

                            globalIndex++;
                        });
                });
            }

            form.addEventListener('click', event => {
                const addButton = event.target.closest('.wp-add-set');
                const removeButton = event.target.closest('.wp-remove-set');

                if (addButton) {
                    const block = addButton.closest('.wp-exercise');
                    const sets = block.querySelector('.wp-sets');

                    if (sets.children.length >= 20) {
                        message.textContent =
                            'Puedes registrar hasta 20 series por ejercicio.';
                        return;
                    }

                    const fragment = template.content.cloneNode(true);
                    const newRow = fragment.querySelector('.wp-set-row');

                    sets.appendChild(fragment);
                    renumberSets();

                    newRow.querySelector('[data-field="reps"]').focus();
                    message.textContent =
                        `Serie añadida a ${block.dataset.label}.`;
                }

                if (removeButton) {
                    const block = removeButton.closest('.wp-exercise');

                    removeButton.closest('.wp-set-row').remove();
                    renumberSets();

                    block.querySelector('.wp-add-set').focus();
                    message.textContent =
                        `Serie eliminada de ${block.dataset.label}.`;
                }
            });

            form.addEventListener('submit', renumberSets);

            renumberSets();
        });
    </script>
</x-app-layout>
