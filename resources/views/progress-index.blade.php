<x-app-layout>
    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-dashboard.css') }}"
    >

    <link
    rel="stylesheet"
    href="{{ asset('css/trackfit-exercise-picker.css') }}"
>

<script
    src="{{ asset('js/trackfit-exercise-picker.js') }}"
    defer
></script>

    <style>
        .tf-progress-page .tf-card:hover {
            transform: none;
        }

        .tf-progress-page .tf-progress-search select:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .tf-progress-page .tf-progress-filter-help {
            margin: 8px 0 0;
            color: #b9a9c9;
            font-size: 12px;
            line-height: 1.5;
        }

        .tf-progress-page .tf-progress-chart-box {
            position: relative;
        }

        .tf-progress-page .tf-progress-explanation {
            margin: 16px 0 0;
            color: #b9a9c9;
            font-size: 12px;
            line-height: 1.7;
        }

        .tf-progress-page .tf-pill input {
            display: block;
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            opacity: 0;
        }

        .tf-progress-page .tf-pill input:focus-visible + span {
            outline: 2px solid #c4b5fd;
            outline-offset: 3px;
        }
    </style>

    @php
        $periodOptions = [
            'week' => 'Semanal',
            'month' => 'Mensual',
            'quarter' => 'Trimestral',
            'year' => 'Anual',
        ];

        $hasRows = count($periodRows) > 0;
    @endphp

    <div class="tf-dashboard tf-progress-page">
        <h1 class="tf-title">Progreso</h1>

        <p class="tf-subtitle">
            Consulta tu evolución por ejercicio o el volumen general.
        </p>

        <form
            id="progressForm"
            method="GET"
            action="{{ route('progress.index') }}"
            class="tf-progress-shell"
        >
            <div class="tf-progress-topbar">
                <div class="tf-progress-search">
                    <label class="tf-label" for="exerciseSelect">
                        Ejercicio
                    </label>

                    <select
                        id="exerciseSelect"
                        name="exercise"
                        class="tf-select"
                    >
                        <option value="">Selecciona un ejercicio</option>

                        @foreach ($exerciseIds as $id)
                            <option
                                value="{{ $id }}"
                                @selected($selectedExercise === $id)
                            >
                                {{ $exerciseNames[$id] ?? $id }}
                            </option>
                        @endforeach
                    </select>

                    <p class="tf-progress-filter-help" id="exerciseHelp">
                        Selecciona «Por ejercicio» para consultar uno concreto.
                    </p>
                </div>

                <div class="tf-progress-mode">
                    <span class="tf-label" id="progressModeLabel">
                        Modo
                    </span>

                    <div
                        class="tf-pill-group"
                        role="radiogroup"
                        aria-labelledby="progressModeLabel"
                    >
                        <label class="tf-pill">
                            <input
                                type="radio"
                                name="mode"
                                value="general"
                                @checked($mode === 'general')
                            >
                            <span>Volumen general</span>
                        </label>

                        <label class="tf-pill">
                            <input
                                type="radio"
                                name="mode"
                                value="exercise"
                                @checked($mode === 'exercise')
                            >
                            <span>Por ejercicio</span>
                        </label>
                    </div>
                </div>

                <div class="tf-progress-period">
                    <span class="tf-label" id="progressPeriodLabel">
                        Periodo
                    </span>

                    <div
                        class="tf-pill-group"
                        role="radiogroup"
                        aria-labelledby="progressPeriodLabel"
                    >
                        @foreach ($periodOptions as $value => $label)
                            <label class="tf-pill">
                                <input
                                    type="radio"
                                    name="group_by"
                                    value="{{ $value }}"
                                    @checked($groupBy === $value)
                                >
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="tf-progress-submit">
                    <button type="submit" class="tf-btn">
                        Buscar
                    </button>
                </div>
            </div>

            <div class="tf-progress-body">
                <div class="tf-progress-left tf-card">
                    <h2 class="tf-progress-panel-title">
                        {{ $mode === 'general'
                            ? 'Volumen por periodo'
                            : ($exerciseNames[$selectedExercise] ?? 'Evolución del ejercicio') }}
                    </h2>

                    <div class="tf-progress-list">
                        @forelse ($periodRows as $row)
                            <div class="tf-progress-item">
                                <div class="tf-progress-item-main">
                                    <div class="tf-progress-item-period">
                                        {{ $row['label'] }}
                                    </div>

                                    <div class="tf-progress-item-sub">
                                        Volumen:
                                        {{ round($row['volume'], 1) }} kg
                                    </div>

                                    @if ($mode === 'exercise')
                                        <div class="tf-progress-item-sub">
                                            Mayor peso:
                                            {{ round($row['best_weight'], 1) }} kg
                                            · Máximas reps:
                                            {{ $row['best_reps'] }}
                                        </div>
                                    @endif

                                    <div class="tf-progress-item-sub">
                                        Entrenos: {{ $row['workouts'] }}
                                        · Series: {{ $row['sets'] }}
                                    </div>
                                </div>

                                @if ($row['diff'] !== null)
                                    <div
                                        class="tf-progress-item-diff {{ $row['diff'] >= 0 ? 'up' : 'down' }}"
                                        title="Diferencia respecto al periodo anterior con registros"
                                    >
                                        {{ $row['diff'] > 0 ? '+' : '' }}{{ round($row['diff'], 1) }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="tf-progress-empty">
                                @if ($mode === 'exercise' && ! $selectedExercise)
                                    Selecciona un ejercicio y pulsa «Buscar».
                                @elseif ($mode === 'exercise')
                                    Todavía no hay series registradas
                                    para este ejercicio.
                                @else
                                    Todavía no hay series registradas
                                    para mostrar el volumen general.
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="tf-progress-right tf-card">
                    <h2 class="tf-progress-panel-title">
                        Gráfico de volumen
                    </h2>

                    <div class="tf-progress-chart-box">
                        @if ($hasRows)
                            <canvas
                                id="progressChart"
                                role="img"
                                aria-label="Evolución del volumen de entrenamiento. Los valores también aparecen en la lista por periodos."
                            ></canvas>
                        @else
                            <div class="tf-progress-chart-empty">
                                @if ($mode === 'exercise' && ! $selectedExercise)
                                    Elige un ejercicio para consultar su gráfico.
                                @else
                                    El gráfico aparecerá cuando haya
                                    series registradas para esta consulta.
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="tf-progress-summary-grid">
                        <div class="tf-progress-summary-card">
                            <div class="tf-progress-summary-label">
                                Periodo con mayor volumen
                            </div>
                            <div class="tf-progress-summary-value">
                                {{ $bestPeriodLabel ?? '—' }}
                            </div>
                        </div>

                        <div class="tf-progress-summary-card">
                            <div class="tf-progress-summary-label">
                                Mayor volumen
                            </div>
                            <div class="tf-progress-summary-value">
                                {{ $bestPeriodVolume !== null
                                    ? round($bestPeriodVolume, 1) . ' kg'
                                    : '—' }}
                            </div>
                        </div>

                        <div class="tf-progress-summary-card">
                            <div class="tf-progress-summary-label">
                                Último periodo con registros
                            </div>
                            <div class="tf-progress-summary-value">
                                {{ $lastPeriodLabel ?? '—' }}
                            </div>
                        </div>

                        <div class="tf-progress-summary-card">
                            <div class="tf-progress-summary-label">
                                Último volumen
                            </div>
                            <div class="tf-progress-summary-value">
                                {{ $lastPeriodVolume !== null
                                    ? round($lastPeriodVolume, 1) . ' kg'
                                    : '—' }}
                            </div>
                        </div>
                    </div>

                    <p class="tf-progress-explanation">
                        Volumen registrado = suma de peso × repeticiones
                        de las series. En el modo por ejercicio,
                        el mayor peso y las máximas repeticiones
                        pueden corresponder a series distintas.
                    </p>
                </div>
            </div>
        </form>
    </div>

    <script>
        (() => {
            function initializeProgressFilters() {
                const form = document.getElementById('progressForm');
                const select = document.getElementById('exerciseSelect');
                const help = document.getElementById('exerciseHelp');

                if (!form || !select || !help) return;

                const generalRadio = form.querySelector(
                    'input[name="mode"][value="general"]'
                );

                const exerciseRadio = form.querySelector(
                    'input[name="mode"][value="exercise"]'
                );

                function updateFilters() {
                    const isGeneral = generalRadio.checked;

                    select.disabled = isGeneral;
                    select.required = !isGeneral;

                    help.textContent = isGeneral
                        ? 'Se incluyen todos los ejercicios registrados.'
                        : 'Elige un ejercicio y pulsa «Buscar».';
                }

                generalRadio.addEventListener('change', updateFilters);
                exerciseRadio.addEventListener('change', updateFilters);

                updateFilters();
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializeProgressFilters,
                    { once: true }
                );
            } else {
                initializeProgressFilters();
            }
        })();
    </script>

    @if ($hasRows)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            (() => {
                const canvas = document.getElementById('progressChart');

                if (!canvas || typeof Chart === 'undefined') return;

                new Chart(canvas, {
                    type: 'line',

                    data: {
                        labels: {{ Illuminate\Support\Js::from($chartLabels) }},

                        datasets: [{
                            label: 'Volumen registrado (kg)',
                            data: {{ Illuminate\Support\Js::from($chartData) }},
                            tension: 0.25,
                            fill: true,
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124, 58, 237, 0.12)',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }],
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        locale: 'es-ES',

                        plugins: {
                            legend: {
                                labels: {
                                    color: '#fff',
                                },
                            },
                        },

                        scales: {
                            x: {
                                ticks: {
                                    color: 'rgba(255,255,255,.75)',
                                },
                                grid: {
                                    color: 'rgba(255,255,255,.06)',
                                },
                            },

                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgba(255,255,255,.75)',
                                },
                                grid: {
                                    color: 'rgba(255,255,255,.06)',
                                },
                            },
                        },
                    },
                });
            })();
        </script>
    @endif
</x-app-layout>