<x-app-layout>
    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-dashboard.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-home.css') }}"
    >

    <div class="th-home">

        {{-- CABECERA PRINCIPAL --}}
        <section
            class="th-hero"
            style="background-image: url('{{ asset('img/img1.png') }}');"
            aria-labelledby="home-title"
        >
            <div class="th-container th-hero-inner">
                <div class="th-hero-copy">
                    <p class="th-eyebrow">
                        Tu espacio de entrenamiento
                    </p>

                    <p class="th-greeting">
                        Hola, {{ auth()->user()->name }}
                    </p>

                    <h1 id="home-title">
                        Tu entrenamiento.<br>
                        Tu alimentación.<br>
                        <span>Tu progreso.</span>
                    </h1>

                    <p class="th-hero-description">
                        Organiza tus rutinas, registra lo que comes
                        y sigue tu evolución. Todo conectado
                        en un mismo lugar.
                    </p>

                    <div class="th-actions">
                        <a
                            class="th-button th-button-primary"
                            href="{{ route('rutina') }}"
                        >
                            Ir a mi rutina
                            <span aria-hidden="true">→</span>
                        </a>

                        <a
                            class="th-button th-button-secondary"
                            href="{{ route('nutrition.today') }}"
                        >
                            Ver mi alimentación
                        </a>
                    </div>
                </div>

                <aside class="th-start" aria-labelledby="start-title">
                    <span class="th-small-label">¿Por dónde empezamos?</span>

                    <h2 id="start-title">Haz que hoy cuente</h2>

                    <p>
                        Elige un apartado y continúa
                        con tu seguimiento.
                    </p>

                    <a href="{{ route('rutina') }}" class="th-start-link">
                        <span class="th-start-number" aria-hidden="true">01</span>
                        <span>
                            <strong>Entrenamiento</strong>
                            <small>Organiza y registra tus sesiones</small>
                        </span>
                        <span class="th-arrow" aria-hidden="true">↗</span>
                    </a>

                    <a
                        href="{{ route('nutrition.today') }}"
                        class="th-start-link"
                    >
                        <span class="th-start-number" aria-hidden="true">02</span>
                        <span>
                            <strong>Alimentación</strong>
                            <small>Consulta tus objetivos del día</small>
                        </span>
                        <span class="th-arrow" aria-hidden="true">↗</span>
                    </a>

                    <a
                        href="{{ route('progress.index') }}"
                        class="th-start-link"
                    >
                        <span class="th-start-number" aria-hidden="true">03</span>
                        <span>
                            <strong>Progreso</strong>
                            <small>Revisa tus registros y evolución</small>
                        </span>
                        <span class="th-arrow" aria-hidden="true">↗</span>
                    </a>
                </aside>
            </div>
        </section>

        {{-- HOY TOCA --}}
        <div class="th-container">
            @include('partials.today-workout')
        </div>

        {{-- PRESENTACIÓN --}}
        <section class="th-section">
            <div class="th-container th-split">
                <div class="th-copy">
                    <p class="th-eyebrow">Todo conectado</p>

                    <h2>
                        Menos aplicaciones.<br>
                        Más claridad.
                    </h2>

                    <p>
                        TrackFit reúne tu entrenamiento, alimentación
                        y progreso para que puedas consultar
                        tus registros sin cambiar de herramienta.
                    </p>

                    <p>
                        Prepara tu semana, apunta lo que haces
                        y revisa tu evolución con una visión conjunta.
                    </p>

                    <div class="th-tags" aria-label="Apartados de TrackFit">
                        <span>Rutinas</span>
                        <span>Nutrición</span>
                        <span>Seguimiento</span>
                    </div>
                </div>

                <div class="th-media">
                    <img
                        src="{{ asset('img/img2.jpg') }}"
                        alt="Persona consultando su móvil en el gimnasio"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            </div>
        </section>

        {{-- RUTINA --}}
        <section class="th-section th-section-tinted">
            <div class="th-container th-split th-image-first">
                <div class="th-copy">
                    <p class="th-eyebrow">01 / Entrenamiento</p>

                    <h2>
                        Una rutina organizada.<br>
                        Un objetivo claro.
                    </h2>

                    <p>
                        Crea tu planificación semanal o utiliza una rutina
                        predefinida. Organiza los ejercicios por día
                        y registra tus sesiones.
                    </p>

                    <ul class="th-feature-list">
                        <li>Planificación por días de entrenamiento.</li>
                        <li>Registro de series, repeticiones y peso.</li>
                        <li>Rutinas predefinidas para empezar.</li>
                    </ul>

                    <a
                        href="{{ route('rutina') }}"
                        class="th-button th-button-primary"
                    >
                        Explorar mis rutinas
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="th-media">
                    <img
                        src="{{ asset('img/img3.jpg') }}"
                        alt="Persona entrenando con cuerdas en el gimnasio"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            </div>
        </section>

        {{-- PROGRESO --}}
        <section class="th-section">
            <div class="th-container th-split">
                <div class="th-copy">
                    <p class="th-eyebrow">02 / Progreso</p>

                    <h2>
                        No te quedes con<br>
                        la sensación. Mira los datos.
                    </h2>

                    <p>
                        Consulta tu evolución por ejercicio, compara
                        el volumen entre periodos y revisa tus récords.
                    </p>

                    <ul class="th-feature-list">
                        <li>Seguimiento de los ejercicios registrados.</li>
                        <li>Comparación por semanas y meses.</li>
                        <li>Récords personales y 1RM estimado.</li>
                    </ul>

                    <div class="th-actions">
                        <a
                            href="{{ route('progress.index') }}"
                            class="th-button th-button-primary"
                        >
                            Consultar mi progreso
                            <span aria-hidden="true">→</span>
                        </a>

                        <a
                            href="{{ route('progress.records') }}"
                            class="th-button th-button-secondary"
                        >
                            🏆 Mis récords
                        </a>
                    </div>
                </div>

                <div class="th-media">
                    <img
                        src="{{ asset('img/img4.jpg') }}"
                        alt="Persona realizando un ejercicio con mancuerna"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            </div>
        </section>

        {{-- NUTRICIÓN --}}
        <section class="th-section th-section-tinted">
            <div class="th-container th-split th-image-first">
                <div class="th-copy">
                    <p class="th-eyebrow">03 / Nutrición</p>

                    <h2>
                        Tu alimentación,<br>
                        también bajo seguimiento.
                    </h2>

                    <p>
                        Configura tus objetivos, registra tus comidas
                        y consulta cómo se distribuyen las calorías
                        y los macronutrientes del día.
                    </p>

                    <ul class="th-feature-list">
                        <li>Registro de alimentos y cantidades.</li>
                        <li>Resumen de calorías, proteínas, hidratos y grasas.</li>
                        <li>Ajuste del objetivo según la actividad registrada.</li>
                    </ul>

                    <a
                        href="{{ route('nutrition.today') }}"
                        class="th-button th-button-primary"
                    >
                        Ver mi nutrición
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="th-media">
                    <img
                        src="{{ asset('img/img5.jpg') }}"
                        alt="Selección de verduras, legumbres, cereales y frutos secos"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            </div>
        </section>

        {{-- CIERRE --}}
        <footer class="th-footer">
            <div class="th-container th-footer-inner">
                <p class="th-brand">Track<span>Fit</span></p>
                <p>Entrena. Registra. Revisa tu progreso.</p>

                <a href="{{ route('profile.edit') }}">
                    Gestionar mi perfil →
                </a>
            </div>
        </footer>
    </div>
</x-app-layout>