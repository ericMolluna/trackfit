<x-app-layout>
<link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">

@php
  $isFirstSetup = empty($profile);
@endphp

<div class="tf-dashboard">

  <div class="tf-nutrition-setup-page">

    {{-- =========================================
         CABECERA
    ========================================== --}}

    @if($isFirstSetup)

      <div class="tf-onboarding-head">

        <div class="tf-onboarding-icon" aria-hidden="true">
          👋
        </div>

        <p class="tf-home-kicker">
          PRIMEROS PASOS
        </p>

        <h1 class="tf-title">
          Bienvenido a TrackFit
        </h1>

        <p class="tf-subtitle">
          Antes de empezar necesitamos algunos datos básicos para
          personalizar tu entrenamiento y nutrición.
        </p>

      </div>

    @else

      <div class="tf-onboarding-head">

        <p class="tf-home-kicker">
          TU PERFIL
        </p>

        <h1 class="tf-title">
          Modificar datos y objetivos
        </h1>

        <p class="tf-subtitle">
          Actualiza tus datos para recalcular tus objetivos nutricionales.
        </p>

      </div>

    @endif


    {{-- =========================================
         EXPLICACIÓN SOLO PARA USUARIOS NUEVOS
    ========================================== --}}

    @if($isFirstSetup)

      <div class="tf-onboarding-info">

        <div class="tf-onboarding-info-item">
          <span aria-hidden="true">🏋️</span>

          <div>
            <strong>Entrenamiento</strong>
            <small>
              Estimaremos el gasto energético de tus sesiones.
            </small>
          </div>
        </div>


        <div class="tf-onboarding-info-item">
          <span aria-hidden="true">🍽️</span>

          <div>
            <strong>Nutrición</strong>
            <small>
              Calcularemos tus calorías y macronutrientes.
            </small>
          </div>
        </div>


        <div class="tf-onboarding-info-item">
          <span aria-hidden="true">📈</span>

          <div>
            <strong>Personalización</strong>
            <small>
              TrackFit adaptará los cálculos a tus características.
            </small>
          </div>
        </div>

      </div>

    @endif


    {{-- =========================================
         FORMULARIO
    ========================================== --}}

    <form
      class="tf-card tf-nutrition-setup-card"
      method="POST"
      action="{{ route('nutrition.setup.save') }}"
    >

      @csrf


      <div class="tf-onboarding-form-head">

        <h2>
          {{ $isFirstSetup ? 'Cuéntanos sobre ti' : 'Tus datos' }}
        </h2>

        <p>
          {{ $isFirstSetup
              ? 'Completa estos datos para configurar TrackFit.'
              : 'Modifica los datos que quieras actualizar.' }}
        </p>

      </div>


      <div class="tf-nutrition-setup-grid">


        {{-- SEXO --}}

        <div class="tf-onboarding-field">

          <label for="sex" class="tf-label">
            <span aria-hidden="true">👤</span>
            Sexo
          </label>

          <select
            id="sex"
            class="tf-select"
            name="sex"
            required
          >

            <option
              value=""
              disabled
              {{ empty($profile) ? 'selected' : '' }}
            >
              Selecciona una opción
            </option>

            <option
              value="male"
              {{ ($profile->sex ?? '') === 'male' ? 'selected' : '' }}
            >
              Hombre
            </option>

            <option
              value="female"
              {{ ($profile->sex ?? '') === 'female' ? 'selected' : '' }}
            >
              Mujer
            </option>

          </select>

        </div>


        {{-- EDAD --}}

        <div class="tf-onboarding-field">

          <label for="age" class="tf-label">
            <span aria-hidden="true">🎂</span>
            Edad
          </label>

          <input
            id="age"
            class="tf-input"
            type="number"
            name="age"
            placeholder="Ej: 25"
            min="10"
            max="90"
            value="{{ old('age', $profile->age ?? '') }}"
            required
          >

        </div>


        {{-- ALTURA --}}

        <div class="tf-onboarding-field">

          <label for="height_cm" class="tf-label">
            <span aria-hidden="true">📏</span>
            Altura
          </label>

          <div class="tf-onboarding-input-unit">

            <input
              id="height_cm"
              class="tf-input"
              type="number"
              name="height_cm"
              placeholder="Ej: 175"
              min="120"
              max="230"
              value="{{ old('height_cm', $profile->height_cm ?? '') }}"
              required
            >

            <span>cm</span>

          </div>

        </div>


        {{-- PESO --}}

        <div class="tf-onboarding-field">

          <label for="weight_kg" class="tf-label">
            <span aria-hidden="true">⚖️</span>
            Peso
          </label>

          <div class="tf-onboarding-input-unit">

            <input
              id="weight_kg"
              class="tf-input"
              type="number"
              step="0.1"
              name="weight_kg"
              placeholder="Ej: 70"
              min="35"
              max="250"
              value="{{ old('weight_kg', $profile->weight_kg ?? '') }}"
              required
            >

            <span>kg</span>

          </div>

        </div>


        {{-- ACTIVIDAD --}}

        <div class="tf-onboarding-field">

          <label for="activity" class="tf-label">
            <span aria-hidden="true">🏃</span>
            Actividad habitual
          </label>

          <select
            id="activity"
            class="tf-select"
            name="activity"
            required
          >

            <option
              value=""
              disabled
              {{ empty($profile) ? 'selected' : '' }}
            >
              Selecciona tu actividad
            </option>

            <option
              value="sedentary"
              {{ ($profile->activity ?? '') === 'sedentary' ? 'selected' : '' }}
            >
              Sedentaria
            </option>

            <option
              value="light"
              {{ ($profile->activity ?? '') === 'light' ? 'selected' : '' }}
            >
              Ligera · 1-3 días/semana
            </option>

            <option
              value="moderate"
              {{ ($profile->activity ?? '') === 'moderate' ? 'selected' : '' }}
            >
              Moderada · 3-5 días/semana
            </option>

            <option
              value="active"
              {{ ($profile->activity ?? '') === 'active' ? 'selected' : '' }}
            >
              Alta · 6-7 días/semana
            </option>

            <option
              value="very_active"
              {{ ($profile->activity ?? '') === 'very_active' ? 'selected' : '' }}
            >
              Muy alta · 2 sesiones/día
            </option>

          </select>

        </div>


        {{-- OBJETIVO --}}

        <div class="tf-onboarding-field">

          <label for="goal" class="tf-label">
            <span aria-hidden="true">🎯</span>
            Objetivo
          </label>

          <select
            id="goal"
            class="tf-select"
            name="goal"
            required
          >

            <option
              value=""
              disabled
              {{ empty($profile) ? 'selected' : '' }}
            >
              Selecciona tu objetivo
            </option>

            <option
              value="lose"
              {{ ($profile->goal ?? '') === 'lose' ? 'selected' : '' }}
            >
              Perder grasa
            </option>

            <option
              value="maintain"
              {{ ($profile->goal ?? '') === 'maintain' ? 'selected' : '' }}
            >
              Mantener peso
            </option>

            <option
              value="gain"
              {{ ($profile->goal ?? '') === 'gain' ? 'selected' : '' }}
            >
              Ganar músculo
            </option>

          </select>

        </div>

      </div>


      {{-- =========================================
           INFORMACIÓN
      ========================================== --}}

      <div class="tf-onboarding-note">

        <span aria-hidden="true">ℹ️</span>

        <p>
          Estos datos se utilizan para calcular tus objetivos
          nutricionales y realizar estimaciones de gasto energético.
          Podrás modificarlos más adelante.
        </p>

      </div>


      {{-- ERRORES --}}

      @if ($errors->any())

        <div
          class="tf-error"
          style="margin-top:16px;"
          role="alert"
        >
          ❌ {{ $errors->first() }}
        </div>

      @endif


      {{-- BOTÓN --}}

      <div class="tf-nutrition-setup-actions">

        <button
          class="tf-btn tf-onboarding-submit"
          type="submit"
        >

          @if($isFirstSetup)
            Empezar en TrackFit →
          @else
            🔄 Recalcular objetivos
          @endif

        </button>

      </div>

    </form>

  </div>

</div>

</x-app-layout>