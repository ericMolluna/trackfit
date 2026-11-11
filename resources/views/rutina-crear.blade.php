<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">

    <div class="tf-dashboard">
        <h1 class="tf-title">Crear rutina</h1>
        <p class="tf-subtitle">Elige días y añade ejercicios.</p>

        @if (session('ok'))
            <p style="color: rgba(255,255,255,.8); margin-bottom: 18px;">
                ✅ {{ session('ok') }}
            </p>
        @endif

        <form method="POST" action="{{ route('rutina.store') }}" class="tf-card" style="max-width: 900px; width: 100%;">
            @csrf

            <label class="tf-label">Nombre de la rutina</label>
            <input class="tf-input" type="text" name="routine_name" placeholder="Ej: Push/Pull/Legs" value="{{ old('routine_name') }}" required>

            <div style="height: 18px;"></div>

            <label class="tf-label">Días que entrenas</label>
            <div class="tf-days">
                @php
                    $dayLabels = [
                        'mon' => 'L', 'tue' => 'M', 'wed' => 'X', 'thu' => 'J', 'fri' => 'V', 'sat' => 'S', 'sun' => 'D'
                    ];
                    $oldDays = old('days', []);
                @endphp

                @foreach($dayLabels as $key => $label)
                    <label class="tf-day">
                        <input type="checkbox" name="days[]" value="{{ $key }}" @checked(in_array($key, $oldDays))>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <div style="height: 26px;"></div>

            <label class="tf-label">Ejercicios</label>

            <div class="tf-row-tools">
                <select id="exerciseSelect" class="tf-input" style="max-width: 420px;">
                    @foreach($exercises as $ex)
                        <option value="{{ $ex }}">{{ $ex }}</option>
                    @endforeach
                </select>

                <button class="tf-btn tf-btn-small" type="button" id="addExerciseBtn">
                    + Añadir
                </button>
            </div>

            <div id="exerciseList" class="tf-ex-list">
                {{-- Si hay old() rehidratamos --}}
                @php $oldEx = old('exercises', []); @endphp
                @foreach($oldEx as $i => $exRow)
                    <div class="tf-ex-item">
                        <span class="tf-ex-name">{{ $exRow['name'] ?? '' }}</span>
                        <input type="hidden" name="exercises[{{ $i }}][name]" value="{{ $exRow['name'] ?? '' }}">
                        <button type="button" class="tf-ex-remove">Quitar</button>
                    </div>
                @endforeach
            </div>

            @if ($errors->any())
                <div class="tf-error" style="margin-top:16px;">
                    ❌ {{ $errors->first() }}
                </div>
            @endif

            <button class="tf-btn" type="submit" style="margin-top: 22px;">
                Guardar (placeholder)
            </button>

            <div style="margin-top:24px;">
                <a href="{{ route('rutina') ?? url('/rutina') }}" class="tf-link">⬅ Volver</a>
            </div>
        </form>
    </div>

    <script>
        (function(){
            const select = document.getElementById('exerciseSelect');
            const addBtn = document.getElementById('addExerciseBtn');
            const list = document.getElementById('exerciseList');

            function currentCount(){
                return list.querySelectorAll('.tf-ex-item').length;
            }

            function addExercise(name){
                const idx = currentCount();

                const item = document.createElement('div');
                item.className = 'tf-ex-item';

                item.innerHTML = `
                    <span class="tf-ex-name"></span>
                    <input type="hidden" name="exercises[${idx}][name]">
                    <button type="button" class="tf-ex-remove">Quitar</button>
                `;

                item.querySelector('.tf-ex-name').textContent = name;
                item.querySelector('input').value = name;

                item.querySelector('.tf-ex-remove').addEventListener('click', () => {
                    item.remove();
                    // Reindexar names para que Laravel reciba array limpio
                    Array.from(list.querySelectorAll('.tf-ex-item')).forEach((el, newIndex) => {
                        const input = el.querySelector('input[type="hidden"]');
                        input.name = `exercises[${newIndex}][name]`;
                    });
                });

                list.appendChild(item);
            }

            addBtn?.addEventListener('click', () => {
                const name = select.value;
                // evitar duplicados simples
                const exists = Array.from(list.querySelectorAll('.tf-ex-name')).some(s => s.textContent === name);
                if (!exists) addExercise(name);
            });
        })();
    </script>
</x-app-layout>
