{{--
    «Hoy toca»: se incluye en dashboard.blade.php con
    @include('partials.today-workout')
    Los datos los envía DashboardController@index.
--}}
@isset($todayKey)
<style>
    .tf-today,
    .tf-today * {
        box-sizing: border-box;
    }

    .tf-today {
        padding-top: 48px;
        padding-bottom: 16px;
    }

    .tf-today-card {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        padding: 28px;
        border: 1px solid rgba(124, 58, 237, .45);
        border-radius: 22px;
        background:
            linear-gradient(145deg, rgba(124, 58, 237, .10), transparent 50%),
            #0b0b0d;
        color: #fff;
        text-align: left;
    }

    .tf-today-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 22px;
    }

    .tf-today-kicker {
        margin: 0 0 6px;
        color: #b89be4;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .tf-today-title {
        margin: 0;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 800;
    }

    .tf-today-links {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tf-today-links a {
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        padding: 8px 12px;
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 10px;
        color: #e4d9f0;
        font-size: .8rem;
        text-decoration: none;
    }

    .tf-today-links a:hover {
        border-color: #a78bfa;
        background: rgba(124, 58, 237, .12);
    }

    .tf-today-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));
        gap: 14px;
    }

    .tf-today-routine {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 16px;
        background: rgba(255, 255, 255, .03);
    }

    .tf-today-routine-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .tf-today-routine-name {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .tf-today-badge {
        flex-shrink: 0;
        padding: 4px 9px;
        border-radius: 8px;
        background: rgba(34, 197, 94, .14);
        color: #86efac;
        font-size: .72rem;
        font-weight: 700;
    }

    .tf-today-meta {
        margin: 0;
        color: rgba(255, 255, 255, .62);
        font-size: .82rem;
    }

    .tf-today-exercises {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .tf-today-exercises li {
        padding: 5px 9px;
        border-radius: 8px;
        background: rgba(124, 58, 237, .12);
        color: #ddd0ee;
        font-size: .76rem;
    }

    .tf-today-routine form {
        margin: 4px 0 0;
    }

    .tf-today-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 46px;
        padding: 10px 16px;
        border: 1px solid #7c3aed;
        border-radius: 12px;
        background: #7c3aed;
        color: #fff;
        font: inherit;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .tf-today-btn:hover {
        background: #6d28d9;
    }

    .tf-today-btn-outline {
        background: transparent;
    }

    .tf-today-btn-outline:hover {
        background: rgba(124, 58, 237, .14);
    }

    .tf-today-btn:focus-visible,
    .tf-today-select:focus-visible,
    .tf-today-links a:focus-visible {
        outline: 2px solid #c4b5fd;
        outline-offset: 3px;
    }

    .tf-today-empty {
        padding: 22px;
        border: 1px dashed rgba(255, 255, 255, .18);
        border-radius: 16px;
        color: rgba(255, 255, 255, .75);
        line-height: 1.6;
    }

    .tf-today-empty strong {
        display: block;
        margin-bottom: 4px;
        color: #fff;
        font-size: 1.05rem;
    }

    .tf-today-empty .tf-today-btn {
        width: auto;
        margin-top: 14px;
    }

    .tf-today-other {
        margin-top: 22px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, .08);
    }

    .tf-today-other label {
        display: block;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, .75);
        font-size: .82rem;
    }

    .tf-today-other-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
    }

    .tf-today-select {
        width: 100%;
        min-width: 0;
        min-height: 46px;
        padding: 10px 12px;
        border: 1px solid rgba(124, 58, 237, .5);
        border-radius: 12px;
        background: #100c16;
        color: #fff;
        font: inherit;
        font-size: .9rem;
    }

    .tf-today-other-row .tf-today-btn {
        width: auto;
    }

    @media (max-width: 640px) {
        .tf-today {
            padding-top: 28px;
        }

        .tf-today-card {
            padding: 20px 14px;
            border-radius: 18px;
        }

        .tf-today-other-row {
            grid-template-columns: minmax(0, 1fr);
        }

        .tf-today-other-row .tf-today-btn,
        .tf-today-empty .tf-today-btn {
            width: 100%;
        }

        .tf-today-select {
            font-size: 16px;
        }
    }
</style>

<section class="tf-today tf-home-container" aria-labelledby="todayTitle">
    <div class="tf-today-card">
        <div class="tf-today-head">
            <div>
                <p class="tf-today-kicker">
                    Hoy · {{ $dayNames[$todayKey] }}
                </p>

                <h2 id="todayTitle" class="tf-today-title">
                    Hoy toca
                </h2>
            </div>

            <nav class="tf-today-links" aria-label="Accesos de entrenamiento">
                <a href="{{ route('rutinas.index') }}">Gestionar rutinas</a>
                <a href="{{ route('progress.records') }}">🏆 Récords</a>
            </nav>
        </div>

        @if ($errors->has('routine_day_id'))
            <p class="tf-today-meta" role="alert">
                {{ $errors->first('routine_day_id') }}
            </p>
        @endif

        @if ($routineDays->isEmpty())
            <div class="tf-today-empty">
                <strong>Todavía no tienes rutinas</strong>
                Crea tu primera rutina y aquí verás qué te toca entrenar cada día.

                <div>
                    <a href="{{ route('rutina.create.step1', ['nuevo' => 1]) }}" class="tf-today-btn">
                        + Crear rutina
                    </a>
                </div>
            </div>
        @elseif ($todayDays->isEmpty())
            <div class="tf-today-empty">
                <strong>Hoy es día de descanso</strong>

                @if ($nextDayKey)
                    Próximo entreno: {{ $dayNames[$nextDayKey] }} ·
                    {{ $nextDays->map(fn ($d) => $d->routine->name)->unique()->implode(', ') }}.
                @endif

                Si te apetece entrenar igualmente, elige una rutina abajo.
            </div>
        @else
            <div class="tf-today-list">
                @foreach ($todayDays as $routineDay)
                    @php
                        $isDone = in_array($routineDay->id, $doneToday);
                        $exerciseLabels = $routineDay->exercises
                            ->map(fn ($ex) => $exerciseNames[$ex->exercise_id] ?? $ex->exercise_id);
                    @endphp

                    <article class="tf-today-routine">
                        <div class="tf-today-routine-top">
                            <h3 class="tf-today-routine-name">
                                {{ $routineDay->routine->name }}
                            </h3>

                            @if ($isDone)
                                <span class="tf-today-badge">✓ Hecho hoy</span>
                            @endif
                        </div>

                        <p class="tf-today-meta">
                            {{ $exerciseLabels->count() }}
                            {{ $exerciseLabels->count() === 1 ? 'ejercicio' : 'ejercicios' }}
                        </p>

                        <ul class="tf-today-exercises">
                            @foreach ($exerciseLabels->take(4) as $label)
                                <li>{{ $label }}</li>
                            @endforeach

                            @if ($exerciseLabels->count() > 4)
                                <li>+{{ $exerciseLabels->count() - 4 }} más</li>
                            @endif
                        </ul>

                        <form method="POST" action="{{ route('workout.start', $routineDay) }}">
                            @csrf

                            <button
                                type="submit"
                                class="tf-today-btn {{ $isDone ? 'tf-today-btn-outline' : '' }}"
                            >
                                ▶ {{ $isDone ? 'Entrenar otra vez' : 'Empezar entreno' }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($routineDays->isNotEmpty())
            <form
                method="POST"
                action="{{ route('dashboard.start') }}"
                class="tf-today-other"
            >
                @csrf

                <label for="todayOther">
                    {{ $todayDays->isEmpty() ? '¿Qué quieres entrenar?' : '¿Prefieres entrenar otra cosa hoy?' }}
                </label>

                <div class="tf-today-other-row">
                    <select id="todayOther" name="routine_day_id" class="tf-today-select" required>
                        <option value="" disabled selected>Elige rutina y día</option>

                        @foreach ($routineDays->groupBy(fn ($d) => $d->routine->id) as $daysOfRoutine)
                            <optgroup label="{{ $daysOfRoutine->first()->routine->name }}">
                                @foreach ($daysOfRoutine as $routineDay)
                                    <option value="{{ $routineDay->id }}">
                                        {{ $dayNames[$routineDay->day] ?? $routineDay->day }}
                                        · {{ $routineDay->exercises->count() }} ejercicios
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>

                    <button type="submit" class="tf-today-btn tf-today-btn-outline">
                        ▶ Empezar
                    </button>
                </div>
            </form>
        @endif
    </div>
</section>
@endisset
