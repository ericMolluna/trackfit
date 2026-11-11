<x-app-layout>

  <link
    rel="stylesheet"
    href="{{ asset('css/trackfit-dashboard.css') }}"
  >

  <div class="tf-dashboard">

    <div class="tf-predefined-header">

      <div>
        <h1 class="tf-title">
          Rutinas predefinidas
        </h1>

        <p class="tf-subtitle">
          Elige una rutina diseñada para empezar a entrenar directamente.
          Podrás consultar sus días y ejercicios antes de seleccionarla.
        </p>
      </div>

    </div>


    @if(count($routines) > 0)

      <div class="tf-predefined-grid">

        @foreach($routines as $key => $routine)

          <article class="tf-predefined-card">

            <div class="tf-predefined-card-top">

              <span class="tf-predefined-number">
                RUTINA {{ $routine['number'] }}
              </span>

              <span class="tf-predefined-days">
                📅 {{ $routine['days_count'] }}
                {{ $routine['days_count'] === 1 ? 'día' : 'días' }}
              </span>

            </div>


            <div class="tf-predefined-icon" aria-hidden="true">

              @if($key === 'upper-lower-4')
                💪
              @elseif($key === 'ppl-5')
                🔥
              @elseif($key === 'fullbody-4')
                🏋️
              @else
                🏋️
              @endif

            </div>


            <h2 class="tf-predefined-title">
              {{ $routine['title'] }}
            </h2>


            <p class="tf-predefined-description">
              {{ $routine['description'] }}
            </p>


            <div class="tf-predefined-day-list">

              @foreach($routine['days'] as $day)

                <span>
                  {{ $day['name'] }}
                </span>

              @endforeach

            </div>


            <a
              href="{{ route('routines.predefined.show', $key) }}"
              class="tf-btn tf-predefined-btn"
            >
              Ver rutina
              <span aria-hidden="true">→</span>
            </a>

          </article>

        @endforeach

      </div>

    @else

      <div class="tf-card">

        <p>
          No hay rutinas predefinidas disponibles.
        </p>

      </div>

    @endif


    <div class="tf-predefined-back">

      <a
        href="{{ route('rutina.follow') }}"
        class="tf-link"
      >
        ← Volver a rutinas
      </a>

    </div>

  </div>

</x-app-layout>