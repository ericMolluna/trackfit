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
        $draft = $wiz ?? session('routine_wizard', []);

        $dayLabels = [
            'mon' => 'Lunes',
            'tue' => 'Martes',
            'wed' => 'Miércoles',
            'thu' => 'Jueves',
            'fri' => 'Viernes',
            'sat' => 'Sábado',
            'sun' => 'Domingo',
        ];

        $selectedDays = old('days', $draft['days'] ?? []);
    @endphp

    <div class="tf-dashboard tf-wizard-page">
        <h1 class="tf-title">Crear rutina</h1>

        <p class="tf-subtitle">
            Paso 1: elige días de entrenamiento.
        </p>

        <form
            method="POST"
            action="{{ route('rutina.create.postStep1') }}"
            class="tf-card tf-wizard-step-card"
        >
            @csrf

            <label for="routineName" class="tf-label">
                Nombre de la rutina
            </label>

            <input
                id="routineName"
                class="tf-input"
                name="routine_name"
                value="{{ old('routine_name', $draft['name'] ?? '') }}"
                placeholder="Ej: Push/Pull/Legs"
                maxlength="60"
                required
            >

            <fieldset class="tf-wizard-days-field">
                <legend class="tf-label">
                    Días que entrenas
                </legend>

                <div class="tf-days">
                    @foreach ($dayLabels as $key => $label)
                        <label class="tf-day">
                            <input
                                type="checkbox"
                                name="days[]"
                                value="{{ $key }}"
                                @checked(
                                    in_array($key, $selectedDays, true)
                                )
                            >

                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            @if ($errors->any())
                <div class="tf-error tf-wizard-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="tf-wizard-actions">
                <a href="{{ url('/rutina') }}" class="tf-link">
                    ← Volver
                </a>

                <button class="tf-btn" type="submit">
                    Siguiente →
                </button>
            </div>

            @if (!empty($draft))
                <p class="tf-wizard-note">
                    Se conservarán los ejercicios elegidos
                    mientras editas este borrador.
                </p>
            @endif
        </form>
    </div>
</x-app-layout>