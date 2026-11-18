<x-app-layout>
    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-dashboard.css') }}"
    >

    <style>
        .tf-food-page .tf-card:hover {
            transform: none;
        }

        .tf-food-page .tf-food-picker {
            position: relative;
            width: 100%;
            margin-bottom: 18px;
            text-align: left;
        }

        .tf-food-page .tf-food-picker .tf-food-input {
            width: 100%;
            margin: 0;
            padding: 14px 16px;
            box-sizing: border-box;
            border: 1px solid #735096;
            border-radius: 13px;
            background: #0c0910;
            color: #fff;
        }

        .tf-food-page .tf-food-picker .tf-food-input:focus {
            border-color: #a78bfa;
            outline: none;
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.16);
        }

        .tf-food-page .tf-food-input::placeholder {
            color: #9789a5;
        }

        .tf-food-page .tf-food-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 40;
            max-height: 260px;
            margin: 0;
            padding: 6px;
            overflow-y: auto;
            list-style: none;
            border: 1px solid #7950a0;
            border-radius: 13px;
            background: #17111e;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.55);
            scrollbar-width: thin;
            scrollbar-color: #8051ad #17111e;
        }

        .tf-food-page .tf-food-results[hidden] {
            display: none;
        }

        .tf-food-page .tf-food-option {
            padding: 12px 14px;
            border-radius: 8px;
            color: #eee6f7;
            font-size: 14px;
            line-height: 1.5;
            overflow-wrap: anywhere;
            cursor: pointer;
        }

        .tf-food-page .tf-food-option[aria-selected="true"] {
            background: #7133b6;
            color: #fff;
        }

        .tf-food-page .tf-food-empty {
            padding: 14px;
            color: #c0b2cd;
            font-size: 13px;
            line-height: 1.6;
        }

        .tf-food-page .tf-food-help {
            margin: 9px 0 18px;
            color: #aa9bb9;
            font-size: 12px;
            line-height: 1.6;
            text-align: left;
        }

        .tf-food-page .tf-select {
            color-scheme: dark;
        }

        .tf-food-page .tf-select option {
            background: #17111e;
            color: #fff;
        }

        .tf-food-page .tf-food-sr {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .tf-food-page .tf-food-back {
            margin-top: 18px;
        }
    </style>

    <div class="tf-dashboard tf-food-page">
        <h1 class="tf-title">Registrar comida</h1>

        <p class="tf-subtitle">Día {{ $date }}</p>

        <div class="tf-nutri-grid">

            {{-- FORMULARIO --}}
            <div class="tf-card" style="color:white;">
                <h2 style="margin-top:0;">Añadir comida</h2>

                <form
                    method="POST"
                    action="{{ route('nutrition.add') }}"
                    class="tf-form"
                >
                    @csrf

                    <input type="hidden" name="date" value="{{ $date }}">

                    <label class="tf-label" for="foodSearch">
                        Buscar alimento
                    </label>

                    <div class="tf-food-picker" id="foodPicker">
                        <input
                            id="foodSearch"
                            class="tf-input tf-food-input"
                            name="food_name"
                            type="text"
                            value="{{ old('food_name') }}"
                            placeholder="Ej.: pollo, arroz, plátano…"
                            autocomplete="off"
                            spellcheck="false"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="foodResults"
                            aria-describedby="foodSearchHelp"
                            required
                        >

                        <ul
                            id="foodResults"
                            class="tf-food-results"
                            role="listbox"
                            aria-label="Alimentos encontrados"
                            hidden
                        ></ul>

                        <span
                            id="foodSearchStatus"
                            class="tf-food-sr"
                            role="status"
                            aria-live="polite"
                        ></span>
                    </div>

                    <p class="tf-food-help" id="foodSearchHelp">
                        Escribe para buscar y selecciona un resultado.
                        También puedes utilizar las flechas del teclado y Enter.
                    </p>

                    <div class="tf-row">
                        <div class="tf-col">
                            <label class="tf-label" for="meal">
                                Comida
                            </label>

                            <select
                                id="meal"
                                class="tf-select"
                                name="meal"
                                required
                            >
                                <option
                                    value="breakfast"
                                    @selected(old('meal', 'breakfast') === 'breakfast')
                                >
                                    Desayuno
                                </option>

                                <option
                                    value="lunch"
                                    @selected(old('meal') === 'lunch')
                                >
                                    Comida
                                </option>

                                <option
                                    value="snack"
                                    @selected(old('meal') === 'snack')
                                >
                                    Merienda
                                </option>

                                <option
                                    value="dinner"
                                    @selected(old('meal') === 'dinner')
                                >
                                    Cena
                                </option>
                            </select>
                        </div>

                        <div class="tf-col">
                            <label class="tf-label" for="grams">
                                Gramos
                            </label>

                            <input
                                id="grams"
                                class="tf-input"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                name="grams"
                                value="{{ old('grams') }}"
                                placeholder="Ej.: 120"
                                required
                            >
                        </div>
                    </div>

                    @if ($errors->any())
                        <div
                            class="tf-error"
                            style="margin-top:14px;"
                            role="alert"
                        >
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button
                        class="tf-btn"
                        type="submit"
                        style="margin-top:14px;"
                    >
                        Guardar comida
                    </button>
                </form>

                <div class="tf-food-back">
                    <a
                        class="tf-link"
                        href="{{ route('nutrition.day', $date) }}"
                    >
                        ← Volver
                    </a>
                </div>
            </div>

            {{-- MACROS Y REGISTROS --}}
            <div class="tf-card" style="color:white;">
                <h2 style="margin-top:0;">Macros de hoy</h2>

                @php
                    $macros = [
                        [
                            'label' => 'Calorías',
                            'value' => $tot['kcal'],
                            'goal' => $goal->calories,
                            'unit' => 'kcal',
                            'decimals' => 0,
                        ],
                        [
                            'label' => 'Proteína',
                            'value' => $tot['p'],
                            'goal' => $goal->protein_g,
                            'unit' => 'g',
                            'decimals' => 1,
                        ],
                        [
                            'label' => 'Carbohidratos',
                            'value' => $tot['c'],
                            'goal' => $goal->carbs_g,
                            'unit' => 'g',
                            'decimals' => 1,
                        ],
                        [
                            'label' => 'Grasas',
                            'value' => $tot['f'],
                            'goal' => $goal->fat_g,
                            'unit' => 'g',
                            'decimals' => 1,
                        ],
                    ];

                    $mealNames = [
                        'breakfast' => 'Desayuno',
                        'lunch' => 'Comida',
                        'snack' => 'Merienda',
                        'dinner' => 'Cena',
                    ];
                @endphp

                @foreach ($macros as $macro)
                    @php
                        $percentage = $macro['goal'] > 0
                            ? max(0, min(100, ($macro['value'] / $macro['goal']) * 100))
                            : 0;
                    @endphp

                    <div class="tf-macro">
                        <div class="tf-macro-title">
                            {{ $macro['label'] }}
                            ({{ round($macro['value'], $macro['decimals']) }}
                            / {{ $macro['goal'] }} {{ $macro['unit'] }})
                        </div>

                        <div class="tf-progress" aria-hidden="true">
                            <div
                                class="tf-progress-fill"
                                style="width: {{ $percentage }}%;"
                            ></div>
                        </div>
                    </div>
                @endforeach

                <hr style="margin:16px 0; opacity:.2;">

                <h3 style="margin:0 0 10px;">
                    Registrado hoy
                </h3>

                @forelse ($logs as $l)
                    @php
                        $m = $l->grams / 100;
                        $k = $l->food->kcal_100 * $m;
                        $p = $l->food->protein_100 * $m;
                        $c = $l->food->carbs_100 * $m;
                        $f = $l->food->fat_100 * $m;
                    @endphp

                    <div class="tf-log-item">
                        <div>
                            <div style="font-weight:700;">
                                {{ $l->food->name }}

                                <span style="opacity:.6;">
                                    ({{ $l->grams }} g)
                                </span>
                            </div>

                            <div style="opacity:.7; font-size:13px;">
                                {{ $mealNames[$l->meal] ?? $l->meal }} ·
                                {{ round($k) }} kcal ·
                                P {{ round($p, 1) }} ·
                                C {{ round($c, 1) }} ·
                                G {{ round($f, 1) }}
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('nutrition.remove', $l) }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="tf-btn tf-btn-small"
                            >
                                Eliminar
                            </button>
                        </form>
                    </div>
                @empty
                    <div style="opacity:.7;">
                        Aún no has añadido comidas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        (() => {
            function initializeFoodSearch() {
                const picker = document.getElementById('foodPicker');
                const input = document.getElementById('foodSearch');
                const results = document.getElementById('foodResults');
                const status = document.getElementById('foodSearchStatus');

                if (!picker || !input || !results || !status) return;

                const foods = {{ Illuminate\Support\Js::from(
                    $foods->pluck('name')->unique()->values()
                ) }};

                const normalize = value => value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLocaleLowerCase('es')
                    .trim();

                const searchableFoods = foods.map(name => ({
                    name,
                    search: normalize(name),
                }));

                let matches = [];
                let activeIndex = -1;

                function closeResults() {
                    results.hidden = true;
                    input.setAttribute('aria-expanded', 'false');
                    input.removeAttribute('aria-activedescendant');
                    activeIndex = -1;

                    results.querySelectorAll('[role="option"]').forEach(option => {
                        option.setAttribute('aria-selected', 'false');
                    });
                }

                function activate(index) {
                    activeIndex = index;

                    const options = results.querySelectorAll('[role="option"]');

                    options.forEach((option, optionIndex) => {
                        option.setAttribute(
                            'aria-selected',
                            optionIndex === index ? 'true' : 'false'
                        );
                    });

                    const activeOption = options[index];

                    if (activeOption) {
                        input.setAttribute(
                            'aria-activedescendant',
                            activeOption.id
                        );

                        activeOption.scrollIntoView({
                            block: 'nearest',
                        });
                    }
                }

                function selectFood(index) {
                    if (!matches[index]) return;

                    input.value = matches[index].name;
                    closeResults();
                    status.textContent = `Alimento seleccionado: ${input.value}`;
                }

                function updateResults() {
                    const query = normalize(input.value);

                    results.replaceChildren();
                    input.removeAttribute('aria-activedescendant');
                    activeIndex = -1;
                    matches = [];

                    if (!query) {
                        closeResults();
                        status.textContent = '';
                        return;
                    }

                    matches = searchableFoods
                        .filter(food => food.search.includes(query))
                        .slice(0, 30);

                    if (!matches.length) {
                        const empty = document.createElement('li');
                        empty.className = 'tf-food-empty';
                        empty.setAttribute('role', 'presentation');
                        empty.textContent =
                            'No encontramos alimentos con ese nombre.';

                        results.appendChild(empty);
                        status.textContent =
                            'No encontramos alimentos con ese nombre.';
                    } else {
                        matches.forEach((food, index) => {
                            const option = document.createElement('li');

                            option.id = `food-option-${index}`;
                            option.className = 'tf-food-option';
                            option.setAttribute('role', 'option');
                            option.setAttribute('aria-selected', 'false');
                            option.textContent = food.name;

                            option.addEventListener('pointerenter', () => {
                                activate(index);
                            });

                            option.addEventListener('click', () => {
                                selectFood(index);
                                input.focus();
                                closeResults();
                            });

                            results.appendChild(option);
                        });

                        status.textContent =
                            `${matches.length} resultados mostrados.`;
                    }

                    results.hidden = false;
                    results.scrollTop = 0;
                    input.setAttribute('aria-expanded', 'true');
                }

                input.addEventListener('input', updateResults);
                input.addEventListener('focus', updateResults);
                input.addEventListener('click', () => {
                    if (results.hidden) updateResults();
                });

                input.addEventListener('keydown', event => {
                    if (event.key === 'Escape') {
                        closeResults();
                        return;
                    }

                    if (event.key === 'Tab') {
                        closeResults();
                        return;
                    }

                    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault();

                        if (results.hidden) updateResults();
                        if (!matches.length) return;

                        const nextIndex = event.key === 'ArrowDown'
                            ? (activeIndex + 1) % matches.length
                            : (activeIndex <= 0
                                ? matches.length - 1
                                : activeIndex - 1);

                        activate(nextIndex);
                    }

                    if (
                        event.key === 'Enter' &&
                        !results.hidden &&
                        activeIndex >= 0
                    ) {
                        event.preventDefault();
                        selectFood(activeIndex);
                    }
                });

                // Mantener el foco en el buscador al pulsar un resultado.
                results.addEventListener('mousedown', event => {
                    event.preventDefault();
                });

                document.addEventListener('click', event => {
                    if (!picker.contains(event.target)) {
                        closeResults();
                    }
                });

                input.addEventListener('blur', () => {
                    closeResults();
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializeFoodSearch,
                    { once: true }
                );
            } else {
                initializeFoodSearch();
            }
        })();
    </script>
</x-app-layout>