<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">

    <div class="tf-dashboard">

        <h1 class="tf-title">Rutina de Gimnasio</h1>

        <p class="tf-subtitle">
            Gestiona tus entrenamientos desde aquí.
        </p>

        <div class="tf-grid">

            <!-- MIS RUTINAS -->
            <a href="{{ route('rutina.follow') }}" class="tf-card">
                <h2>📋 Mis rutinas</h2>
                <p>Consulta tus rutinas guardadas y elige cuál entrenar.</p>
            </a>

            <!-- CREAR RUTINA (?nuevo=1 descarta borradores anteriores) -->
            <a href="{{ route('rutina.create.step1', ['nuevo' => 1]) }}" class="tf-card purple">
                <h2>➕ Crear rutina</h2>
                <p>Diseña un nuevo plan semanal de entrenamiento.</p>
            </a>

        </div>

        <div style="margin-top:60px;">
            <a href="{{ route('dashboard') }}" class="tf-link">⬅ Volver al Dashboard</a>
        </div>

    </div>
</x-app-layout>