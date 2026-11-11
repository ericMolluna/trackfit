<x-app-layout>
    @php
        $formatDate = fn ($date) => $date
            ? \Illuminate\Support\Carbon::parse($date)->format('d/m/Y')
            : '';
    @endphp

    <link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">

    <style>
        .tf-records,
        .tf-records * {
            box-sizing: border-box;
        }

        .tf-records .tf-subtitle {
            max-width: 100%;
        }

        .tf-records-wrap {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            text-align: left;
        }

        .tf-records-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            margin-bottom: 14px;
        }

        .tf-records-search {
            width: 100%;
            min-height: 48px;
            padding: 10px 14px;
            border: 1px solid rgba(124, 58, 237, .5);
            border-radius: 12px;
            background: #100c16;
            color: #fff;
            font: inherit;
            font-size: .9rem;
        }

        .tf-records-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 10px 16px;
            border: 1px solid rgba(124, 58, 237, .7);
            border-radius: 12px;
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }

        .tf-records-back:hover {
            background: rgba(124, 58, 237, .14);
        }

        .tf-records-note {
            margin: 0 0 18px;
            color: rgba(255, 255, 255, .58);
            font-size: .8rem;
            line-height: 1.6;
        }

        .tf-records-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr));
            gap: 14px;
        }

        .tf-record-card {
            padding: 18px;
            border: 1px solid rgba(124, 58, 237, .35);
            border-radius: 16px;
            background: #0b0b0d;
        }

        .tf-record-name {
            margin: 0 0 14px;
            color: #fff;
            font-size: 1rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .tf-record-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .tf-record-stat {
            padding: 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .035);
        }

        .tf-record-stat-label {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, .55);
            font-size: .7rem;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .tf-record-stat-value {
            display: block;
            color: #d8c2fb;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .tf-record-stat-detail {
            display: block;
            margin-top: 3px;
            color: rgba(255, 255, 255, .6);
            font-size: .75rem;
        }

        .tf-record-sets {
            margin: 12px 0 0;
            color: rgba(255, 255, 255, .5);
            font-size: .75rem;
        }

        .tf-records-empty {
            padding: 28px;
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 16px;
            color: rgba(255, 255, 255, .75);
            text-align: center;
            line-height: 1.6;
        }

        @media (max-width: 640px) {
            .tf-records-toolbar {
                grid-template-columns: minmax(0, 1fr);
            }

            .tf-records-search {
                font-size: 16px;
            }
        }
    </style>

    <div class="tf-dashboard tf-records">
        <h1 class="tf-title">🏆 Récords personales</h1>

        <p class="tf-subtitle">
            Tu mejor marca y tu 1RM estimado en cada ejercicio.
        </p>

        <div class="tf-records-wrap">
            <div class="tf-records-toolbar">
                <label for="recordSearch" class="sr-only">Buscar ejercicio</label>

                <input
                    id="recordSearch"
                    type="search"
                    class="tf-records-search"
                    placeholder="Buscar ejercicio…"
                    autocomplete="off"
                >

                <a href="{{ route('progress.index') }}" class="tf-records-back">
                    ← Volver a progreso
                </a>
            </div>

            <p class="tf-records-note">
                1RM estimado con la fórmula de Epley: peso × (1 + reps / 30).
                Solo se calcula con series de 1 a {{ $maxReps }} repeticiones,
                porque con más repeticiones la estimación pierde precisión.
            </p>

            @if ($records->isEmpty())
                <div class="tf-records-empty">
                    Aún no hay series con peso y repeticiones registradas.
                    Completa un entreno y aquí aparecerán tus récords.
                </div>
            @else
                <div class="tf-records-grid" id="recordsGrid">
                    @foreach ($records as $record)
                        <article class="tf-record-card" data-name="{{ $record['name'] }}">
                            <h2 class="tf-record-name">{{ $record['name'] }}</h2>

                            <div class="tf-record-stats">
                                <div class="tf-record-stat">
                                    <span class="tf-record-stat-label">Peso máximo</span>
                                    <span class="tf-record-stat-value">
                                        {{ \App\Support\PersonalRecords::format($record['best_weight']['weight']) }} kg
                                    </span>
                                    <span class="tf-record-stat-detail">
                                        × {{ $record['best_weight']['reps'] }} reps
                                        · {{ $formatDate($record['best_weight']['date']) }}
                                    </span>
                                </div>

                                <div class="tf-record-stat">
                                    <span class="tf-record-stat-label">1RM estimado</span>

                                    @if ($record['best_1rm'])
                                        <span class="tf-record-stat-value">
                                            {{ \App\Support\PersonalRecords::format($record['best_1rm']['value']) }} kg
                                        </span>
                                        <span class="tf-record-stat-detail">
                                            {{ \App\Support\PersonalRecords::format($record['best_1rm']['weight']) }} kg × {{ $record['best_1rm']['reps'] }}
                                            · {{ $formatDate($record['best_1rm']['date']) }}
                                        </span>
                                    @else
                                        <span class="tf-record-stat-value">—</span>
                                        <span class="tf-record-stat-detail">
                                            Sin series de 1–{{ $maxReps }} reps
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="tf-record-sets">
                                {{ $record['sets'] }} {{ $record['sets'] === 1 ? 'serie registrada' : 'series registradas' }}
                            </p>
                        </article>
                    @endforeach
                </div>

                <p class="tf-records-empty" id="recordsNoResults" hidden>
                    No hay ejercicios con ese nombre.
                </p>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('recordSearch');
            const cards = document.querySelectorAll('.tf-record-card');
            const empty = document.getElementById('recordsNoResults');

            if (!search || !cards.length) return;

            const normalize = value => value
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();

            search.addEventListener('input', () => {
                const query = normalize(search.value);
                let visible = 0;

                cards.forEach(card => {
                    const match = normalize(card.dataset.name || '').includes(query);
                    card.hidden = !match;
                    if (match) visible++;
                });

                empty.hidden = visible > 0;
            });
        });
    </script>
</x-app-layout>
