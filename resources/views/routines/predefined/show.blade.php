<x-app-layout>

  <link
    rel="stylesheet"
    href="{{ asset('css/trackfit-dashboard.css') }}"
  >

  <div class="tf-dashboard">
    <div class="tf-predef-nav-top">

  <a
    href="{{ route('rutina.follow') }}"
    class="tf-predef-nav-top-btn"
  >
    ← Volver a mi rutina
  </a>

  <a
    href="{{ route('routines.predefined.index') }}"
    class="tf-predef-nav-top-btn"
  >
    📋 Rutinas predefinidas
  </a>

</div>

    @php
  /*
   * Distribución semanal de cada plantilla.
   * El número indica qué entrenamiento de $routine['days']
   * corresponde a ese día.
   * null = descanso.
   */

  $weeklySchedules = [

    'upper-lower-4' => [
      0,      // Lunes    - Upper A
      1,      // Martes   - Lower A
      null,   // Miércoles - Descanso
      2,      // Jueves   - Upper B
      3,      // Viernes  - Lower B
      null,   // Sábado   - Descanso
      null,   // Domingo  - Descanso
    ],

    'ppl-5' => [
      0,      // Lunes    - Push
      1,      // Martes   - Pull
      2,      // Miércoles - Legs
      null,   // Jueves   - Descanso
      3,      // Viernes  - Upper
      4,      // Sábado   - Lower
      null,   // Domingo  - Descanso
    ],

    'fullbody-4' => [
      0,      // Lunes    - Full Body A
      null,   // Martes   - Descanso
      1,      // Miércoles - Full Body B
      null,   // Jueves   - Descanso
      2,      // Viernes  - Full Body C
      3,      // Sábado   - Full Body D
      null,   // Domingo  - Descanso
    ],

  ];

  $weekNames = [
    'Lunes',
    'Martes',
    'Miércoles',
    'Jueves',
    'Viernes',
    'Sábado',
    'Domingo',
  ];

  $schedule = $weeklySchedules[$routineKey] ?? [];
@endphp


<div class="tf-predefined-detail-grid">

  @foreach($weekNames as $weekIndex => $weekName)

    @php
      $routineDayIndex = $schedule[$weekIndex] ?? null;

      $day = $routineDayIndex !== null
        ? ($routine['days'][$routineDayIndex] ?? null)
        : null;
    @endphp


    @if($day)

      {{-- DÍA DE ENTRENAMIENTO --}}
      <article class="tf-card tf-predefined-day-card">

        <div class="tf-predefined-day-header">

          <span class="tf-predefined-weekday">
            {{ strtoupper($weekName) }}
          </span>

          <h2>
            {{ $day['name'] }}
          </h2>

          <span class="tf-predefined-exercise-count">
            🏋️ {{ count($day['exercises']) }}
            ejercicios
          </span>

        </div>


        <div class="tf-predefined-exercises">

          @foreach($day['exercises'] as $exerciseId)

            <div class="tf-predefined-exercise">

              <span class="tf-predefined-exercise-number">
                {{ $loop->iteration }}
              </span>

              <div>
                <strong>
                  {{ $exerciseNames[$exerciseId] ?? $exerciseId }}
                </strong>

                <small>
                  Ejercicio {{ $loop->iteration }}
                </small>
              </div>

            </div>

          @endforeach

        </div>

      </article>


    @else

      {{-- DÍA DE DESCANSO --}}
      <article class="tf-card tf-predefined-day-card tf-predefined-rest-card">

        <div class="tf-predefined-day-header">

          <span class="tf-predefined-weekday">
            {{ strtoupper($weekName) }}
          </span>

          <h2>
            Descanso
          </h2>

          <span class="tf-predefined-exercise-count">
            Día de recuperación
          </span>

        </div>


        <div class="tf-rest-content">

          <div class="tf-rest-icon">
            💤
          </div>

          <strong>
            Día de descanso
          </strong>

          <p>
            Tu cuerpo también necesita recuperarse
            para seguir progresando.
          </p>

        </div>

      </article>

    @endif

  @endforeach

</div>



   



    <div class="tf-predefined-select">

      <div>

        <h2>
          ¿Quieres utilizar esta rutina?
        </h2>

        <p>
          La rutina se añadirá a tu cuenta y podrás seguirla
          desde TrackFit como cualquier rutina creada por ti.
        </p>

      </div>


      {{--
        TODAVÍA NO CONECTAMOS ESTE BOTÓN.
        Lo haremos en el siguiente paso.
      --}}

      <form
  method="POST"
  action="{{ route('routines.predefined.use', $routineKey) }}"
>
  @csrf

  <button
    type="submit"
    class="tf-btn tf-predefined-use-btn"
  >
    ✓ Usar esta rutina
  </button>
</form>

    </div>


    <div class="tf-predefined-back">

      <a
        href="{{ route('routines.predefined.index') }}"
        class="tf-link"
      >
        ← Ver otras rutinas
      </a>

    </div>

  </div>

</x-app-layout>