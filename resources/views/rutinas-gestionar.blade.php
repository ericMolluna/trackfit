<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">

    <style>
        .tf-manage,
        .tf-manage * {
            box-sizing: border-box;
        }

        .tf-manage .tf-subtitle {
            max-width: 100%;
        }

        .tf-manage-wrap {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            text-align: left;
        }

        .tf-manage-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .tf-manage-count {
            margin: 0;
            color: rgba(255, 255, 255, .65);
            font-size: .88rem;
        }

        .tf-manage-alert {
            margin-bottom: 16px;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: .88rem;
            line-height: 1.5;
        }

        .tf-manage-alert-ok {
            border: 1px solid rgba(34, 197, 94, .4);
            background: rgba(34, 197, 94, .1);
            color: #bbf7d0;
        }

        .tf-manage-alert-error {
            border: 1px solid rgba(239, 68, 68, .4);
            background: rgba(239, 68, 68, .1);
            color: #fecaca;
        }

        .tf-manage-list {
            display: grid;
            gap: 14px;
        }

        .tf-manage-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 18px;
            padding: 20px;
            border: 1px solid rgba(124, 58, 237, .35);
            border-radius: 18px;
            background: #0b0b0d;
        }

        .tf-manage-name {
            margin: 0 0 8px;
            color: #fff;
            font-size: 1.15rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .tf-manage-days {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0 0 8px;
            padding: 0;
            list-style: none;
        }

        .tf-manage-days li {
            padding: 4px 9px;
            border-radius: 8px;
            background: rgba(124, 58, 237, .13);
            color: #ddd0ee;
            font-size: .75rem;
        }

        .tf-manage-meta {
            margin: 0;
            color: rgba(255, 255, 255, .55);
            font-size: .78rem;
        }

        .tf-manage-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
        }

        .tf-manage-actions form {
            margin: 0;
        }

        .tf-manage-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 42px;
            padding: 9px 14px;
            border: 1px solid rgba(124, 58, 237, .7);
            border-radius: 10px;
            background: transparent;
            color: #fff;
            font: inherit;
            font-size: .8rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .tf-manage-btn:hover {
            background: rgba(124, 58, 237, .14);
            border-color: #a78bfa;
        }

        .tf-manage-btn-primary {
            border-color: #7c3aed;
            background: #7c3aed;
        }

        .tf-manage-btn-primary:hover {
            background: #6d28d9;
        }

        .tf-manage-btn-danger {
            border-color: rgba(239, 68, 68, .6);
            color: #fca5a5;
        }

        .tf-manage-btn-danger:hover {
            border-color: #ef4444;
            background: rgba(239, 68, 68, .12);
        }

        .tf-manage-btn:focus-visible {
            outline: 2px solid #c4b5fd;
            outline-offset: 3px;
        }

        .tf-manage-empty {
            padding: 28px;
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 16px;
            color: rgba(255, 255, 255, .75);
            text-align: center;
        }

        .tf-manage-back {
            margin-top: 28px;
            text-align: center;
        }

        @media (max-width: 720px) {
            .tf-manage-card {
                grid-template-columns: minmax(0, 1fr);
                padding: 16px;
            }

            .tf-manage-actions {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .tf-manage-actions .tf-manage-btn,
            .tf-manage-actions form {
                width: 100%;
            }

            .tf-manage-toolbar .tf-manage-btn {
                width: 100%;
            }
        }
    </style>

    <div class="tf-dashboard tf-manage">
        <h1 class="tf-title">Gestionar rutinas</h1>

        <p class="tf-subtitle">
            Edita, duplica o elimina tus rutinas guardadas.
        </p>

        <div class="tf-manage-wrap">
            @if (session('ok'))
                <div class="tf-manage-alert tf-manage-alert-ok" role="status">
                    {{ session('ok') }}
                </div>
            @endif

            @if (session('error'))
                <div class="tf-manage-alert tf-manage-alert-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="tf-manage-toolbar">
                <p class="tf-manage-count">
                    {{ $routines->count() }}
                    {{ $routines->count() === 1 ? 'rutina guardada' : 'rutinas guardadas' }}
                </p>

                <a href="{{ route('rutina.create.step1', ['nuevo' => 1]) }}" class="tf-manage-btn tf-manage-btn-primary">
                    + Crear rutina
                </a>
            </div>

            <div class="tf-manage-list">
                @forelse ($routines as $routine)
                    @php
                        $totalExercises = $routine->days->sum('exercises_count');
                        $workouts = (int) ($workoutCounts[$routine->id] ?? 0);
                    @endphp

                    <article class="tf-manage-card">
                        <div>
                            <h2 class="tf-manage-name">{{ $routine->name }}</h2>

                            <ul class="tf-manage-days" aria-label="Días de la rutina">
                                @foreach ($routine->days as $routineDay)
                                    <li>{{ $dayNames[$routineDay->day] ?? $routineDay->day }}</li>
                                @endforeach
                            </ul>

                            <p class="tf-manage-meta">
                                {{ $totalExercises }} {{ $totalExercises === 1 ? 'ejercicio' : 'ejercicios' }} programados
                                · {{ $workouts }} {{ $workouts === 1 ? 'entreno registrado' : 'entrenos registrados' }}
                            </p>
                        </div>

                        <div class="tf-manage-actions">
                            <a href="{{ route('rutina.follow', ['routine' => $routine]) }}" class="tf-manage-btn tf-manage-btn-primary">
                                ▶ Ver y entrenar
                            </a>

                            <a href="{{ route('rutinas.edit', $routine) }}" class="tf-manage-btn">
                                ✎ Editar
                            </a>

                            <form method="POST" action="{{ route('rutinas.duplicate', $routine) }}">
                                @csrf
                                <input type="hidden" name="from" value="manage">

                                <button type="submit" class="tf-manage-btn">
                                    ⧉ Duplicar
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('rutinas.destroy', $routine) }}"
                                onsubmit="return confirm(@js('¿Eliminar la rutina «' . $routine->name . '»? Tus entrenos registrados y récords se conservarán.'))"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="from" value="manage">

                                <button type="submit" class="tf-manage-btn tf-manage-btn-danger">
                                    🗑 Eliminar
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="tf-manage-empty">
                        Todavía no tienes rutinas. Crea la primera para empezar a entrenar.
                    </div>
                @endforelse
            </div>

            <div class="tf-manage-back">
                <a href="{{ url('/rutina') }}" class="tf-link">⬅ Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>