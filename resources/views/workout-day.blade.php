<x-app-layout>
  <link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">

  <div class="tf-dashboard">

    <div class="tf-workout-header">
      <div>
        <h1 class="tf-title">Entreno</h1>

        <p class="tf-subtitle">
          Registra las series, repeticiones y peso de cada ejercicio.
        </p>
      </div>
    </div>


    <div class="tf-card tf-workout-card">

      @if($workout && $workout->started_at && !$workout->ended_at)

        <form
          method="POST"
          action="{{ route('workout.finish', $routineDay->id) }}"
          id="workoutForm"
        >

          @csrf

          <input
            type="hidden"
            name="workout_id"
            value="{{ $workout->id }}"
          >


          {{-- =====================================
               EJERCICIOS Y SERIES
          ====================================== --}}

          @foreach($routineDay->exercises as $ex)

            @php
              $label = $names[$ex->exercise_id] ?? $ex->exercise_id;
            @endphp

            <div
              class="tf-ex-block"
              data-ex="{{ $ex->exercise_id }}"
            >

              <div class="tf-ex-head">

                <div>

                  <div class="tf-ex-title">
                    🏋️ {{ $label }}
                  </div>

                  <div class="tf-ex-description">
                    Introduce las repeticiones y el peso utilizado en cada serie.
                  </div>

                </div>


                <button
                  type="button"
                  class="tf-btn tf-btn-small tf-add-set"
                >
                  <span aria-hidden="true">＋</span>
                  Añadir serie
                </button>

              </div>


              <div
                class="tf-set-labels"
                aria-hidden="true"
              >
                <span>Serie</span>
                <span>Repeticiones</span>
                <span>Peso</span>
                <span></span>
              </div>


              <div class="tf-sets">

                @for($i = 0; $i < 3; $i++)

                  <div class="tf-set-row">

                    <input
                      type="hidden"
                      name="sets[__IDX__][exercise_id]"
                      value="{{ $ex->exercise_id }}"
                    >

                    <input
                      type="hidden"
                      class="tf-set-no"
                      name="sets[__IDX__][set_no]"
                      value=""
                    >


                    <div
                      class="tf-set-badge"
                      aria-label="Número de serie"
                    >
                      S?
                    </div>


                    <input
                      class="tf-input tf-input-sm"
                      name="sets[__IDX__][reps]"
                      type="number"
                      min="0"
                      max="200"
                      inputmode="numeric"
                      placeholder="Ej: 10"
                      aria-label="Repeticiones"
                    >


                    <div class="tf-weight-input-wrap">

                      <input
                        class="tf-input tf-input-sm"
                        name="sets[__IDX__][weight]"
                        type="number"
                        min="0"
                        max="999.99"
                        step="0.01"
                        inputmode="decimal"
                        placeholder="Ej: 60"
                        aria-label="Peso en kilogramos"
                      >

                      <span class="tf-input-unit">
                        kg
                      </span>

                    </div>


                    <button
                      type="button"
                      class="tf-btn tf-btn-small tf-remove-set"
                      aria-label="Eliminar serie"
                      title="Eliminar serie"
                    >
                      🗑️
                      <span class="tf-remove-text">
                        Quitar
                      </span>
                    </button>

                  </div>

                @endfor

              </div>

            </div>

          @endforeach



          {{-- =====================================
               FINALIZAR ENTRENAMIENTO
          ====================================== --}}

          <div class="tf-workout-finish-section">

            <div class="tf-workout-finish-head">

              <div
                class="tf-workout-finish-icon"
                aria-hidden="true"
              >
                🔥
              </div>

              <div>

                <h2>
                  Finalizar entrenamiento
                </h2>

                <p>
                  Indica la intensidad y tu descanso habitual entre series.
                  TrackFit utilizará estos datos junto con las series realizadas
                  y tu peso para estimar el gasto energético.
                </p>

              </div>

            </div>



            {{-- =====================================
                 INTENSIDAD
            ====================================== --}}

            <fieldset class="tf-intensity-fieldset">

              <legend class="tf-label">
                Intensidad del entrenamiento
              </legend>


              <div class="tf-intensity-grid">


                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="intensity"
                    value="light"
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      🌿
                    </span>

                    <strong>Suave</strong>

                    <small>
                      Sesión tranquila o con poco esfuerzo.
                    </small>

                  </span>

                </label>



                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="intensity"
                    value="moderate"
                    checked
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      ⚡
                    </span>

                    <strong>Normal</strong>

                    <small>
                      Entrenamiento habitual con esfuerzo moderado.
                    </small>

                  </span>

                </label>



                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="intensity"
                    value="intense"
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      🔥
                    </span>

                    <strong>Intenso</strong>

                    <small>
                      Sesión exigente y de alta intensidad.
                    </small>

                  </span>

                </label>

              </div>

            </fieldset>



            {{-- =====================================
                 DESCANSO ENTRE SERIES
            ====================================== --}}

            <fieldset class="tf-intensity-fieldset tf-rest-fieldset">

              <legend class="tf-label">
                ⏱️ Descanso habitual entre series
              </legend>

              <p class="tf-rest-help">
                Selecciona aproximadamente cuánto descansas entre una serie y la siguiente.
              </p>


              <div class="tf-rest-grid">


                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="rest_seconds"
                    value="30"
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      ⚡
                    </span>

                    <strong>&lt; 1 min</strong>

                    <small>
                      Usaremos 30 segundos.
                    </small>

                  </span>

                </label>



                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="rest_seconds"
                    value="60"
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      ⏱️
                    </span>

                    <strong>1 min</strong>

                    <small>
                      Descanso corto.
                    </small>

                  </span>

                </label>



                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="rest_seconds"
                    value="120"
                    checked
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      ⏱️
                    </span>

                    <strong>2 min</strong>

                    <small>
                      Descanso moderado.
                    </small>

                  </span>

                </label>



                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="rest_seconds"
                    value="180"
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      🕒
                    </span>

                    <strong>3 min</strong>

                    <small>
                      Descanso largo.
                    </small>

                  </span>

                </label>



                <label class="tf-intensity-option">

                  <input
                    type="radio"
                    name="rest_seconds"
                    value="300"
                    required
                  >

                  <span class="tf-intensity-card">

                    <span
                      class="tf-intensity-icon"
                      aria-hidden="true"
                    >
                      🧘
                    </span>

                    <strong>&gt; 3 min</strong>

                    <small>
                      Usaremos 5 minutos.
                    </small>

                  </span>

                </label>

              </div>

            </fieldset>



            @if ($errors->any())

              <div
                class="tf-error"
                style="margin-top:16px;"
                role="alert"
              >
                ❌ {{ $errors->first() }}
              </div>

            @endif



            <button
              class="tf-btn tf-finish-btn"
              type="submit"
            >
              ✅ Finalizar y guardar entreno
            </button>

          </div>

        </form>


      @else

        <div class="tf-workout-empty">

          <div
            class="tf-workout-empty-icon"
            aria-hidden="true"
          >
            🏋️
          </div>

          <div>

            <strong>
              No hay ningún entreno activo.
            </strong>

            <p>
              Vuelve a tu rutina y selecciona el día que quieres entrenar.
            </p>

          </div>

        </div>

      @endif



      <div class="tf-workout-back">

        <a
          class="tf-link"
          href="{{ route('rutina.follow') }}"
        >
          ← Volver a mi rutina
        </a>

      </div>

    </div>

  </div>



  <script>
    document.addEventListener('DOMContentLoaded', () => {

      // ==============================================
      // SERIES DINÁMICAS
      // ==============================================

      let globalIdx = 0;


      function renumber(block) {

        const rows =
          block.querySelectorAll('.tf-set-row');


        rows.forEach((row, i) => {

          const n = i + 1;

          const badge =
            row.querySelector('.tf-set-badge');

          const setNo =
            row.querySelector('.tf-set-no');


          if (badge) {

            badge.textContent = `S${n}`;

            badge.setAttribute(
              'aria-label',
              `Serie ${n}`
            );

          }


          if (setNo) {
            setNo.value = n;
          }

        });

      }



      // ==============================================
      // ASIGNAR ÍNDICES INICIALES
      // ==============================================

      document
        .querySelectorAll('.tf-set-row')
        .forEach(row => {

          row
            .querySelectorAll(
              'input[name^="sets[__IDX__]"]'
            )
            .forEach(input => {

              input.name =
                input.name.replace(
                  'sets[__IDX__]',
                  `sets[${globalIdx}]`
                );

            });


          globalIdx++;

        });



      document
        .querySelectorAll('.tf-ex-block')
        .forEach(
          block => renumber(block)
        );



      // ==============================================
      // AÑADIR SERIE
      // ==============================================

      function addSet(block) {

        const exId =
          block.dataset.ex;

        const sets =
          block.querySelector('.tf-sets');

        if (!sets) return;


        const row =
          document.createElement('div');

        row.className =
          'tf-set-row';


        row.innerHTML = `

          <input
            type="hidden"
            name="sets[${globalIdx}][exercise_id]"
            value="${exId}"
          >

          <input
            type="hidden"
            class="tf-set-no"
            name="sets[${globalIdx}][set_no]"
            value=""
          >


          <div
            class="tf-set-badge"
            aria-label="Número de serie"
          >
            S?
          </div>


          <input
            class="tf-input tf-input-sm"
            name="sets[${globalIdx}][reps]"
            type="number"
            min="0"
            max="200"
            inputmode="numeric"
            placeholder="Ej: 10"
            aria-label="Repeticiones"
          >


          <div class="tf-weight-input-wrap">

            <input
              class="tf-input tf-input-sm"
              name="sets[${globalIdx}][weight]"
              type="number"
              min="0"
              max="999.99"
              step="0.01"
              inputmode="decimal"
              placeholder="Ej: 60"
              aria-label="Peso en kilogramos"
            >

            <span class="tf-input-unit">
              kg
            </span>

          </div>


          <button
            type="button"
            class="tf-btn tf-btn-small tf-remove-set"
            aria-label="Eliminar serie"
            title="Eliminar serie"
          >
            🗑️

            <span class="tf-remove-text">
              Quitar
            </span>

          </button>

        `;


        sets.appendChild(row);

        globalIdx++;

        renumber(block);
      }



      // ==============================================
      // EVENTOS AÑADIR / QUITAR
      // ==============================================

      document
        .querySelectorAll('.tf-ex-block')
        .forEach(block => {


          block
            .querySelector('.tf-add-set')
            ?.addEventListener(
              'click',
              () => addSet(block)
            );


          block.addEventListener(
            'click',
            (e) => {

              const rm =
                e.target.closest(
                  '.tf-remove-set'
                );

              if (!rm) return;


              rm
                .closest('.tf-set-row')
                ?.remove();


              renumber(block);

            }
          );

        });

    });
  </script>

</x-app-layout>