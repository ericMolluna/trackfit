<x-app-layout>
  <link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">

  <div class="tf-dashboard">

    <div class="tf-nutrition-head">
      <div>
        <h1 class="tf-title">Nutrición</h1>
        <p class="tf-subtitle">Día {{ $date }}</p>
      </div>

      <div class="tf-nutrition-date-badge" aria-label="Fecha seleccionada">
        📅 {{ $date }}
      </div>
    </div>


    <div class="tf-nutrition-layout">

      {{-- =====================================================
           BALANCE ENERGÉTICO
      ====================================================== --}}

      <section class="tf-card tf-energy-card">

        <div class="tf-section-heading">
          <div class="tf-section-heading-icon">
            🔥
          </div>

          <div>
            <h2>Balance energético</h2>
            <p>
              Tu objetivo se adapta según la actividad que has realizado hoy.
            </p>
          </div>
        </div>


        <div class="tf-energy-grid">

          <div class="tf-energy-item">
            <span class="tf-energy-icon">🎯</span>

            <div>
              <span class="tf-energy-label">Objetivo base</span>
              <strong>{{ $energy['base'] }} kcal</strong>
            </div>
          </div>


          <div class="tf-energy-item">
            <span class="tf-energy-icon">👟</span>

            <div>
              <span class="tf-energy-label">
                Pasos
              </span>

              <strong>
                +{{ $energy['steps'] }} kcal
              </strong>

              <small>
                {{ number_format($activity->steps, 0, ',', '.') }} pasos
              </small>
            </div>
          </div>


          <div class="tf-energy-item">
            <span class="tf-energy-icon">🏋️</span>

            <div>
              <span class="tf-energy-label">
                Entrenamiento
              </span>

              <strong>
                +{{ $energy['workout'] }} kcal
              </strong>

              <small>
                Gasto estimado de tus entrenos
              </small>
            </div>
          </div>


          <div class="tf-energy-item tf-energy-item-main">
            <span class="tf-energy-icon">🍽️</span>

            <div>
              <span class="tf-energy-label">
                Objetivo de hoy
              </span>

              <strong>
                {{ $energy['today'] }} kcal
              </strong>
            </div>
          </div>

        </div>


        {{-- PASOS DEL DÍA --}}

        <div class="tf-steps-box">

          <div class="tf-steps-info">
            <div class="tf-steps-icon">
              👟
            </div>

            <div>
              <strong>Pasos de hoy</strong>

              <p>
                Introduce tus pasos para ajustar el gasto energético del día.
              </p>
            </div>
          </div>


          <form
            method="POST"
            action="{{ route('nutrition.activity.save') }}"
            class="tf-steps-form"
          >
            @csrf

            <input
              type="hidden"
              name="date"
              value="{{ $date }}"
            >

            <label
              for="steps"
              class="sr-only"
            >
              Número de pasos realizados hoy
            </label>

            <input
              id="steps"
              class="tf-input"
              type="number"
              name="steps"
              min="0"
              max="100000"
              step="1"
              inputmode="numeric"
              value="{{ $activity->steps }}"
              placeholder="Ej: 8000"
              required
              aria-describedby="steps-help"
            >

            <button
              class="tf-btn"
              type="submit"
            >
              💾 Guardar pasos
            </button>

          </form>

          <small id="steps-help" class="tf-steps-help">
            El gasto es una estimación aproximada basada en tu peso y número de pasos.
          </small>

        </div>

      </section>


      {{-- =====================================================
           RESUMEN NUTRICIONAL
      ====================================================== --}}

      <section class="tf-card tf-nutrition-summary-card">

        <div class="tf-section-heading">
          <div class="tf-section-heading-icon">
            🥗
          </div>

          <div>
            <h2>Resumen del día</h2>
            <p>
              Compara lo que has consumido con tus objetivos.
            </p>
          </div>
        </div>


        <div class="tf-nutrition-summary-grid">

          {{-- CALORÍAS --}}
          <div class="tf-nutrition-stat">
            <span class="tf-nutrition-stat-icon">
              🔥
            </span>

            <div>
              <span class="tf-nutrition-stat-label">
                Calorías
              </span>

              <strong>
                {{ round($tot['kcal']) }}
                /
                {{ $energy['today'] }}
                kcal
              </strong>
            </div>
          </div>


          {{-- PROTEÍNA --}}
          <div class="tf-nutrition-stat">
            <span class="tf-nutrition-stat-icon">
              💪
            </span>

            <div>
              <span class="tf-nutrition-stat-label">
                Proteína
              </span>

              <strong>
                {{ round($tot['p'],1) }}
                /
                {{ $goal->protein_g }}
                g
              </strong>
            </div>
          </div>


          {{-- CARBOHIDRATOS --}}
          <div class="tf-nutrition-stat">
            <span class="tf-nutrition-stat-icon">
              ⚡
            </span>

            <div>
              <span class="tf-nutrition-stat-label">
                Carbohidratos
              </span>

              <strong>
                {{ round($tot['c'],1) }}
                /
                {{ $goal->carbs_g }}
                g
              </strong>
            </div>
          </div>


          {{-- GRASAS --}}
          <div class="tf-nutrition-stat">
            <span class="tf-nutrition-stat-icon">
              🥑
            </span>

            <div>
              <span class="tf-nutrition-stat-label">
                Grasas
              </span>

              <strong>
                {{ round($tot['f'],1) }}
                /
                {{ $goal->fat_g }}
                g
              </strong>
            </div>
          </div>

        </div>


        {{-- CALORÍAS RESTANTES --}}

        <div class="tf-calorie-remaining">

          @if($energy['remaining'] >= 0)

            <div class="tf-calorie-remaining-icon">
              ✅
            </div>

            <div>
              <span>Calorías disponibles</span>

              <strong>
                {{ $energy['remaining'] }} kcal
              </strong>
            </div>

          @else

            <div class="tf-calorie-remaining-icon">
              ⚠️
            </div>

            <div>
              <span>Has superado el objetivo</span>

              <strong>
                {{ abs($energy['remaining']) }} kcal
              </strong>
            </div>

          @endif

        </div>


        {{-- ACCIONES --}}

        <div class="tf-actions tf-nutrition-actions">

          <a
            class="tf-btn tf-btn-small"
            href="{{ route('nutrition.setup') }}"
          >
            ⚙️ Modificar objetivos y datos
          </a>

          <a
            class="tf-btn tf-btn-small"
            href="{{ route('nutrition.log', $date) }}"
          >
            ➕ Registrar comida
          </a>

        </div>

      </section>

    </div>

  </div>
</x-app-layout>