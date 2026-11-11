<x-app-layout>
    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-dashboard.css') }}"
    >

    @php
        $days = $wiz['days'] ?? [];
        $exerciseNames = config('exercises.names', []);

        $totalDays = count($days);

        $totalExercises = collect($days)->sum(
            fn ($day) => count($wiz['by_day'][$day] ?? [])
        );

        $firstDay = collect($days)->first();
    @endphp

    <style>
        /* Estilos exclusivos del resumen de creación de rutina. */
        .tf-summary-page,
        .tf-summary-page *,
        .tf-summary-page *::before,
        .tf-summary-page *::after {
            box-sizing: border-box;
        }

        .tf-summary-page {
            justify-content: flex-start;
            padding: 44px 20px 64px;
        }

        .tf-summary-page .tf-title {
            margin: 0 0 18px;
            font-size: clamp(2rem, 4vw, 3rem);
        }

        .tf-summary-page .tf-subtitle {
            max-width: 100%;
            margin: 0 0 32px;
            line-height: 1.6;
        }

        .tf-summary-container {
            width: 100%;
            max-width: 1000px;
            min-width: 0;
            padding: 30px;
            border: 1px solid rgba(124, 58, 237, .38);
            border-radius: 24px;
            background:
                linear-gradient(
                    145deg,
                    rgba(124, 58, 237, .06),
                    transparent 45%
                ),
                #090909;
            text-align: left;
        }

        /* Nombre y datos de la rutina. */
        .tf-summary-heading {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, .09);
        }

        .tf-summary-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 54px;
            width: 54px;
            height: 54px;
            border: 1px solid rgba(124, 58, 237, .45);
            border-radius: 17px;
            background: rgba(124, 58, 237, .12);
            color: #c4a2ff;
        }

        .tf-summary-heading-content {
            min-width: 0;
        }

        .tf-summary-kicker {
            display: block;
            margin-bottom: 6px;
            color: #b89be4;
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .tf-summary-name {
            margin: 0;
            color: #fff;
            font-size: clamp(1.3rem, 3vw, 1.8rem);
            font-weight: 800;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .tf-summary-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 13px;
        }

        .tf-summary-meta span {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 5px 10px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 9px;
            background: rgba(255, 255, 255, .035);
            color: #d6ccdf;
            font-size: .76rem;
            line-height: 1.4;
        }

        /* Distribución de días. */
        .tf-summary-days {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: start;
            gap: 16px;
        }

        .tf-summary-day {
            min-width: 0;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 17px;
            background: rgba(255, 255, 255, .025);
        }

        .tf-summary-day-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
            padding-bottom: 13px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .tf-summary-day-title {
            margin: 0;
            color: #fff;
            font-size: 1rem;
            font-weight: 800;
        }

        .tf-summary-day-count {
            padding: 5px 9px;
            border-radius: 8px;
            background: rgba(124, 58, 237, .12);
            color: #cbb3ee;
            font-size: .7rem;
            line-height: 1.4;
        }

        /* Lista de ejercicios. */
        .tf-summary-exercises {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .tf-summary-exercise {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
            min-height: 44px;
            padding: 8px 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .025);
        }

        .tf-summary-exercise-number {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 28px;
            width: 28px;
            height: 28px;
            border: 1px solid rgba(124, 58, 237, .32);
            border-radius: 8px;
            background: rgba(124, 58, 237, .09);
            color: #c3a4ed;
            font-size: .68rem;
            font-weight: 800;
        }

        .tf-summary-exercise-name {
            min-width: 0;
            color: #e6dfec;
            font-size: .85rem;
            font-weight: 600;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .tf-summary-empty {
            margin: 0;
            color: #b9acbf;
            font-size: .85rem;
            line-height: 1.6;
        }

        /* Pie y acciones. */
        .tf-summary-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 26px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, .09);
        }

        .tf-summary-help {
            flex: 1 1 220px;
            margin: 0;
            color: #b8aabd;
            font-size: .8rem;
            line-height: 1.6;
        }

        .tf-summary-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tf-summary-actions form {
            margin: 0;
        }

        .tf-summary-actions .tf-summary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 46px;
            margin: 0;
            padding: 11px 17px;
            border: 1px solid rgba(124, 58, 237, .7);
            border-radius: 12px;
            color: #fff;
            font: inherit;
            font-size: .82rem;
            font-weight: 700;
            line-height: 1.4;
            text-decoration: none;
            cursor: pointer;
            transition:
                background .18s ease,
                border-color .18s ease;
        }

        .tf-summary-actions .tf-summary-button-secondary {
            background: transparent;
            color: #e0d3ee;
        }

        .tf-summary-actions .tf-summary-button-secondary:hover {
            border-color: #a78bfa;
            background: rgba(124, 58, 237, .1);
        }

        .tf-summary-actions .tf-summary-button-primary {
            border-color: #7c3aed;
            background: #7c3aed;
        }

        .tf-summary-actions .tf-summary-button-primary:hover {
            border-color: #8b5cf6;
            background: #6d28d9;
        }

        .tf-summary-actions .tf-summary-button:focus-visible {
            outline: 2px solid #c4b5fd;
            outline-offset: 4px;
        }

        /* Móvil. */
        @media (max-width: 680px) {
            .tf-summary-page {
                padding: 28px 14px 44px;
            }

            .tf-summary-page .tf-title {
                padding: 14px 30px;
            }

            .tf-summary-page .tf-subtitle {
                padding: 11px 18px;
                margin-bottom: 24px;
                font-size: .9rem;
            }

            .tf-summary-container {
                padding: 20px 16px;
                border-radius: 20px;
            }

            .tf-summary-heading {
                align-items: flex-start;
                gap: 12px;
                margin-bottom: 20px;
                padding-bottom: 20px;
            }

            .tf-summary-icon {
                flex-basis: 44px;
                width: 44px;
                height: 44px;
                border-radius: 13px;
            }

            .tf-summary-days {
                grid-template-columns: minmax(0, 1fr);
                gap: 12px;
            }

            .tf-summary-day {
                padding: 15px;
            }

            .tf-summary-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
                margin-top: 20px;
                padding-top: 20px;
            }

            .tf-summary-help {
                flex: auto;
            }

            .tf-summary-actions {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 10px;
            }

            .tf-summary-actions .tf-summary-button,
            .tf-summary-actions form {
                width: 100%;
            }
        }
    </style>

    <div class="tf-dashboard tf-summary-page">
        <h1 class="tf-title">Resumen</h1>

        <p class="tf-subtitle">
            Revisa tu rutina antes de finalizar.
        </p>

        <section
            class="tf-summary-container"
            aria-labelledby="summaryRoutineName"
        >
            <header class="tf-summary-heading">
                <div class="tf-summary-icon" aria-hidden="true">
                    <svg
                        width="26"
                        height="26"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect
                            x="4"
                            y="5"
                            width="16"
                            height="16"
                            rx="3"
                        ></rect>
                        <path d="M8 3v4M16 3v4M4 11h16"></path>
                        <path d="m9 16 2 2 4-4"></path>
                    </svg>
                </div>

                <div class="tf-summary-heading-content">
                    <span class="tf-summary-kicker">
                        Tu rutina
                    </span>

                    <h2
                        id="summaryRoutineName"
                        class="tf-summary-name"
                    >
                        {{ $wiz['name'] }}
                    </h2>

                    <div class="tf-summary-meta">
                        <span>
                            {{ $totalDays }}
                            {{ $totalDays === 1
                                ? 'día de entrenamiento'
                                : 'días de entrenamiento' }}
                        </span>

                        <span>
                            {{ $totalExercises }}
                            {{ $totalExercises === 1
                                ? 'ejercicio programado'
                                : 'ejercicios programados' }}
                        </span>
                    </div>
                </div>
            </header>

            <div class="tf-summary-days">
                @foreach ($days as $day)
                    @php
                        $dayExercises = $wiz['by_day'][$day] ?? [];
                        $dayExerciseCount = count($dayExercises);
                    @endphp

                    <article class="tf-summary-day">
                        <header class="tf-summary-day-header">
                            <h3 class="tf-summary-day-title">
                                {{ $dayNames[$day] ?? $day }}
                            </h3>

                            <span class="tf-summary-day-count">
                                {{ $dayExerciseCount }}
                                {{ $dayExerciseCount === 1
                                    ? 'ejercicio'
                                    : 'ejercicios' }}
                            </span>
                        </header>

                        <ol class="tf-summary-exercises">
                            @forelse ($dayExercises as $exerciseId)
                                <li class="tf-summary-exercise">
                                    <span
                                        class="tf-summary-exercise-number"
                                        aria-hidden="true"
                                    >
                                        {{ str_pad(
                                            (string) $loop->iteration,
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        ) }}
                                    </span>

                                    <span class="tf-summary-exercise-name">
                                        {{ $exerciseNames[$exerciseId]
                                            ?? $exerciseId }}
                                    </span>
                                </li>
                            @empty
                                <li class="tf-summary-empty">
                                    No hay ejercicios seleccionados
                                    para este día.
                                </li>
                            @endforelse
                        </ol>
                    </article>
                @endforeach
            </div>

            <footer class="tf-summary-footer">
                <p class="tf-summary-help">
                    Comprueba los días y los ejercicios.
                    Puedes volver a editarlos antes de finalizar.
                </p>

                <div class="tf-summary-actions">
                    @if ($firstDay !== null)
                        <a
                            href="{{ route('rutina.create.day', [
                                'day' => $firstDay,
                            ]) }}"
                            class="tf-summary-button tf-summary-button-secondary"
                        >
                            <span aria-hidden="true">←</span>
                            Editar días
                        </a>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('rutina.create.finish') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="tf-summary-button tf-summary-button-primary"
                        >
                            Finalizar
                            <span aria-hidden="true">✓</span>
                        </button>
                    </form>
                </div>
            </footer>
        </section>
    </div>
</x-app-layout>