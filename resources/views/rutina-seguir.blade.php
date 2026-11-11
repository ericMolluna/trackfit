<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/trackfit-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/trackfit-routine-edit.css') }}">

    @if (session('ok'))
        <div class="tf-toast tf-toast-success" role="status" aria-live="polite" data-tf-toast>
            <span class="tf-toast-icon" aria-hidden="true">✅</span>

            <div class="tf-toast-content">
                <strong>Operación completada</strong>
                <span>{{ session('ok') }}</span>
            </div>

            <button type="button" class="tf-toast-close" aria-label="Cerrar aviso"
                onclick="this.closest('[data-tf-toast]')?.remove()">×</button>
        </div>
    @elseif (session('error'))
        <div class="tf-toast tf-toast-error" role="alert" aria-live="assertive" data-tf-toast>
            <span class="tf-toast-icon" aria-hidden="true">⚠️</span>

            <div class="tf-toast-content">
                <strong>No se pudo completar la acción</strong>
                <span>{{ session('error') }}</span>
            </div>

            <button type="button" class="tf-toast-close" aria-label="Cerrar aviso"
                onclick="this.closest('[data-tf-toast]')?.remove()">×</button>
        </div>
    @endif

    <div class="tf-dashboard tf-dashboard-spaced tf-routine-follow-page">

        @include('partials.records-toast')

        <div class="tf-predef-nav-top">
            <a href="{{ url('/rutina') }}" class="tf-predef-nav-top-btn">← Volver atrás</a>

            <a href="{{ route('routines.predefined.index') }}" class="tf-predef-nav-top-btn">
                📋 Rutinas predefinidas
            </a>
        </div>

        <div class="tf-page-head">
            <h1 class="tf-title" id="routineTitle">{{ $routine->name }}</h1>

            <p class="tf-subtitle">
                Elige una rutina, consulta sus ejercicios
                y empieza tu entrenamiento cuando estés preparado.
            </p>
        </div>

        {{-- SELECTOR + ACCIONES --}}
        <section class="tf-routine-selector" aria-labelledby="routineSelectorTitle">
            <div class="tf-routine-selector-heading">
                <div>
                    <h2 id="routineSelectorTitle">Mis rutinas</h2>

                    <p>
                        Tienes {{ $routines->count() }}
                        {{ $routines->count() === 1 ? 'rutina guardada.' : 'rutinas guardadas.' }}
                    </p>
                </div>

                <a href="{{ route('rutina.create.step1', ['nuevo' => 1]) }}" class="tf-rbtn">
                    + Crear otra rutina
                </a>
            </div>

                        <form method="GET" action="{{ route('rutina.follow') }}" class="tf-routine-selector-form">
                <div class="tf-routine-selector-field">
                    <label for="savedRoutine" class="tf-label">Rutina que quieres consultar</label>

                    <select id="savedRoutine" name="routine" class="tf-select"
                        onchange="this.form.submit()">
                        @foreach ($routines as $savedRoutine)
                            @php
                                $scheduledDays = $savedRoutine->days
                                    ->map(fn ($d) => $dayNames[$d->day] ?? $d->day)
                                    ->implode(', ');
                            @endphp

                            <option value="{{ $savedRoutine->id }}" @selected($routine->id === $savedRoutine->id)>
                                {{ $savedRoutine->name }}@if ($scheduledDays !== '') — {{ $scheduledDays }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <noscript>
                    <button type="submit" class="tf-btn">Ver rutina</button>
                </noscript>
            </form>

            <div class="tf-routine-actions" id="routineActions">
                <button type="button" class="tf-rbtn tf-rbtn-primary" data-open-editor>
                    ✎ Editar rutina
                </button>

                <form method="POST" action="{{ route('rutinas.duplicate', $routine) }}">
                    @csrf
                    <button type="submit" class="tf-rbtn" style="width:100%">⧉ Duplicar</button>
                </form>

                <button type="button" class="tf-rbtn tf-rbtn-danger" data-open-delete>
                    🗑 Eliminar
                </button>
            </div>
        </section>

        
        @if ($errors->any())
            <div class="tf-routine-alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- VISTA NORMAL --}}
        <div class="tf-routine-follow-wrap" id="routineView">
            @forelse ($routine->days as $d)
                <section class="tf-routine-day-card">
                    <div class="tf-routine-day-top">
                        <div class="tf-routine-day-info">
                            <div class="tf-routine-day-icon" aria-hidden="true">📅</div>

                            <div>
                                <h2 class="tf-routine-day-title">
                                    {{ $dayNames[$d->day] ?? strtoupper($d->day) }}
                                </h2>

                                <p class="tf-routine-day-sub">
                                    🏋️ {{ $d->exercises->count() }}
                                    {{ $d->exercises->count() === 1 ? 'ejercicio' : 'ejercicios' }}
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('workout.start', $d->id) }}">
                            @csrf

                            <button class="tf-btn tf-btn-small tf-start-workout-btn" type="submit">
                                <span class="tf-btn-icon" aria-hidden="true">▶</span>
                                Empezar entreno
                            </button>
                        </form>
                    </div>

                    <div class="tf-routine-ex-list">
                        @forelse ($d->exercises as $ex)
                            @php
                                $label = $names[$ex->exercise_id] ?? $ex->exercise_id;
                                $visual = $exerciseVisuals[$ex->exercise_id] ?? null;
                            @endphp

                            <article class="tf-routine-ex-item">
                                <div class="tf-routine-ex-visual">
                                    @if ($visual)
                                        <img
                                            class="tf-routine-ex-img"
                                            src="{{ asset($visual['frame1']) }}"
                                            data-frame1="{{ asset($visual['frame1']) }}"
                                            data-frame2="{{ asset($visual['frame2']) }}"
                                            alt="Demostración de {{ $label }}"
                                            decoding="async"
                                        >
                                    @else
                                        <div class="tf-routine-ex-no-image" aria-hidden="true">🏋️</div>
                                    @endif
                                </div>

                                <div class="tf-routine-ex-content">
                                    <span class="tf-routine-ex-name">{{ $label }}</span>
                                    <span class="tf-routine-ex-help">Visualiza el movimiento antes de empezar</span>
                                </div>
                            </article>
                        @empty
                            <p class="tf-routine-day-sub">Este día no tiene ejercicios registrados.</p>
                        @endforelse
                    </div>
                </section>
            @empty
                <section class="tf-routine-day-card">
                    <h2 class="tf-routine-day-title">Esta rutina no tiene días registrados</h2>
                    <p class="tf-routine-day-sub">Pulsa «Editar rutina» para añadir días y ejercicios.</p>
                </section>
            @endforelse

            <div class="tf-routine-follow-actions">
                <a class="tf-btn" href="{{ route('routines.predefined.index') }}">
                    <span class="tf-btn-icon" aria-hidden="true">📋</span>
                    Ver rutinas predefinidas
                </a>

                <a class="tf-btn" href="{{ url('/rutina') }}">
                    <span class="tf-btn-icon" aria-hidden="true">←</span>
                    Volver atrás
                </a>
            </div>
        </div>

        {{-- EDITOR (lo genera JavaScript) --}}
        <div id="routineEditor" hidden></div>
    </div>

    {{-- Formulario real que envía los cambios --}}
    <form id="routineUpdateForm" method="POST" action="{{ route('rutinas.update', $routine) }}" hidden>
        @csrf
        @method('PUT')
        <div id="routineUpdateFields"></div>
    </form>

    {{-- DIÁLOGO: AÑADIR EJERCICIOS --}}
    <dialog id="exercisePicker" class="tf-dialog" aria-labelledby="pickerTitle">
        <div class="tf-dialog-inner">
            <div class="tf-dialog-head">
                <h2 id="pickerTitle" class="tf-dialog-title">
                    Añadir ejercicios · <span id="pickerDay"></span>
                </h2>

                <button type="button" class="tf-rbtn tf-rbtn-icon" data-picker-close aria-label="Cerrar">×</button>
            </div>

            <div class="tf-picker-filters">
                <input id="pickerSearch" type="search" class="tf-edit-input"
                    placeholder="Buscar ejercicio…" autocomplete="off" aria-label="Buscar ejercicio">

                <select id="pickerGroup" class="tf-edit-select" aria-label="Grupo muscular">
                    <option value="">Todos los grupos</option>
                </select>
            </div>

            <div id="pickerGrid" class="tf-picker-grid"></div>

            <p id="pickerEmpty" class="tf-picker-empty" hidden>No hay ejercicios con ese nombre.</p>

            <div class="tf-dialog-foot">
                <span id="pickerCount" class="tf-picker-count" aria-live="polite"></span>
                <button type="button" class="tf-rbtn tf-rbtn-primary" data-picker-close>Listo</button>
            </div>
        </div>
    </dialog>

    {{-- DIÁLOGO: ELIMINAR RUTINA --}}
    <dialog id="deleteDialog" class="tf-dialog tf-dialog-small" aria-labelledby="deleteTitle">
        <div class="tf-dialog-inner">
            <h2 id="deleteTitle" class="tf-dialog-title">¿Eliminar «{{ $routine->name }}»?</h2>

            <p class="tf-dialog-text" style="margin-top:10px">
                La rutina desaparecerá de tu lista. Tus entrenos registrados,
                tu progreso y tus récords se conservan.
            </p>

            <div class="tf-dialog-foot">
                <button type="button" class="tf-rbtn" data-delete-close>Cancelar</button>

                <form method="POST" action="{{ route('rutinas.destroy', $routine) }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="tf-rbtn tf-rbtn-danger-solid">🗑 Eliminar rutina</button>
                </form>
            </div>
        </div>
    </dialog>

    <script>
        window.tfRoutineEdit = {
            routine: @json($editData),
            catalog: @json($catalog),
            names: @json($names),
            dayNames: @json($dayNames),
            openEditor: @json($openEditor),
        };
    </script>

    <script>
    (() => {
        const config = window.tfRoutineEdit;
        const DAY_ORDER = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        const view = document.getElementById('routineView');
        const actions = document.getElementById('routineActions');
        const editor = document.getElementById('routineEditor');
        const title = document.getElementById('routineTitle');
        const updateForm = document.getElementById('routineUpdateForm');
        const updateFields = document.getElementById('routineUpdateFields');

        const picker = document.getElementById('exercisePicker');
        const pickerGrid = document.getElementById('pickerGrid');
        const pickerSearch = document.getElementById('pickerSearch');
        const pickerGroup = document.getElementById('pickerGroup');
        const pickerDay = document.getElementById('pickerDay');
        const pickerCount = document.getElementById('pickerCount');
        const pickerEmpty = document.getElementById('pickerEmpty');

        const deleteDialog = document.getElementById('deleteDialog');

        const catalogById = Object.fromEntries(config.catalog.map(ex => [ex.id, ex]));
        const originalTitle = title.textContent;

        const clone = value => JSON.parse(JSON.stringify(value));

        const original = clone(config.routine);
        let state = clone(config.routine);
        let pickerDayIndex = null;
        let saving = false;

        const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[c]));

        const normalize = value => String(value ?? '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();

        const exerciseName = id => catalogById[id]?.name ?? config.names[id] ?? id;
        const isDirty = () => JSON.stringify(state) !== JSON.stringify(original);
        const plural = (n, one, many) => `${n} ${n === 1 ? one : many}`;

        /* ---------------- RENDER DEL EDITOR ---------------- */

        function render(focusKey = null) {
            const usedDays = state.days.map(d => d.day);

            const daysHtml = state.days.map((day, i) => {
                const options = DAY_ORDER.map(key => `
                    <option value="${key}"
                        ${key === day.day ? 'selected' : ''}
                        ${key !== day.day && usedDays.includes(key) ? 'disabled' : ''}>
                        ${esc(config.dayNames[key])}
                    </option>`).join('');

                const exercisesHtml = day.exercises.map((id, j) => {
                    const img = catalogById[id]?.img;
                    const last = j === day.exercises.length - 1;

                    return `
                        <li class="tf-edit-ex">
                            <span class="tf-edit-ex-num" aria-hidden="true">${String(j + 1).padStart(2, '0')}</span>
                            ${img
                                ? `<img class="tf-edit-ex-img" src="${esc(img)}" alt="" loading="lazy">`
                                : `<span class="tf-edit-ex-img" aria-hidden="true"></span>`}
                            <span class="tf-edit-ex-name">${esc(exerciseName(id))}</span>
                            <span class="tf-edit-ex-btns">
                                <button type="button" class="tf-rbtn tf-rbtn-icon"
                                    data-action="up" data-day="${i}" data-ex="${j}"
                                    data-focus-key="up-${i}-${esc(id)}"
                                    aria-label="Subir ${esc(exerciseName(id))}" ${j === 0 ? 'disabled' : ''}>↑</button>
                                <button type="button" class="tf-rbtn tf-rbtn-icon"
                                    data-action="down" data-day="${i}" data-ex="${j}"
                                    data-focus-key="down-${i}-${esc(id)}"
                                    aria-label="Bajar ${esc(exerciseName(id))}" ${last ? 'disabled' : ''}>↓</button>
                                <button type="button" class="tf-rbtn tf-rbtn-icon tf-rbtn-danger"
                                    data-action="remove-ex" data-day="${i}" data-ex="${j}"
                                    aria-label="Quitar ${esc(exerciseName(id))}">×</button>
                            </span>
                        </li>`;
                }).join('');

                return `
                    <section class="tf-edit-day" aria-label="${esc(config.dayNames[day.day])}">
                        <div class="tf-edit-day-head">
                            <select class="tf-edit-select" data-action="day-change" data-day="${i}"
                                aria-label="Día de la semana">${options}</select>
                            <button type="button" class="tf-rbtn tf-rbtn-danger"
                                data-action="remove-day" data-day="${i}"
                                ${state.days.length === 1 ? 'disabled' : ''}>Quitar día</button>
                        </div>

                        <p class="tf-edit-day-count">${plural(day.exercises.length, 'ejercicio', 'ejercicios')}</p>

                        ${day.exercises.length
                            ? `<ol class="tf-edit-ex-list">${exercisesHtml}</ol>`
                            : `<p class="tf-edit-empty">Este día todavía no tiene ejercicios.</p>`}

                        <button type="button" class="tf-rbtn tf-rbtn-add"
                            data-action="add-ex" data-day="${i}" data-focus-key="add-${i}">
                            + Añadir ejercicios
                        </button>
                    </section>`;
            }).join('');

            editor.innerHTML = `
                <div class="tf-edit">
                    <span class="tf-edit-kicker">Modo edición</span>
                    <h2 class="tf-edit-title">Editar rutina</h2>
                    <p class="tf-edit-help">
                        Cambia el nombre, los días y los ejercicios.
                        Nada se guarda hasta que pulses «Guardar cambios».
                        Si quitas un día, su historial de entrenos se conserva.
                    </p>

                    <label class="tf-edit-label" for="editName">Nombre de la rutina</label>
                    <input id="editName" class="tf-edit-input" maxlength="60" value="${esc(state.name)}">

                    <div class="tf-edit-days">${daysHtml}</div>

                    <button type="button" class="tf-rbtn tf-edit-add-day" data-action="add-day"
                        data-focus-key="add-day" ${state.days.length >= 7 ? 'disabled' : ''}>
                        + Añadir día
                    </button>

                    <p class="tf-edit-error" id="editError" role="alert"></p>

                    <div class="tf-edit-bar">
                        <span class="tf-edit-status">${isDirty() ? 'Tienes cambios sin guardar' : 'Sin cambios'}</span>
                        <div class="tf-edit-bar-btns">
                            <button type="button" class="tf-rbtn" data-action="cancel">Cancelar</button>
                            <button type="button" class="tf-rbtn tf-rbtn-primary" data-action="save">Guardar cambios</button>
                        </div>
                    </div>
                </div>`;

            if (focusKey) {
                editor.querySelector(`[data-focus-key="${CSS.escape(focusKey)}"]`)?.focus();
            }
        }

        function updateStatus() {
            const status = editor.querySelector('.tf-edit-status');
            if (status) status.textContent = isDirty() ? 'Tienes cambios sin guardar' : 'Sin cambios';
        }

        /* ---------------- ABRIR / CERRAR ---------------- */

        function openEditor() {
            render();
            view.hidden = true;
            actions.hidden = true;
            editor.hidden = false;
            editor.scrollIntoView({ behavior: 'smooth', block: 'start' });
            editor.querySelector('#editName')?.focus({ preventScroll: true });
        }

        function closeEditor() {
            state = clone(original);
            title.textContent = originalTitle;
            editor.hidden = true;
            editor.innerHTML = '';
            view.hidden = false;
            actions.hidden = false;

            const url = new URL(window.location.href);
            url.searchParams.delete('editar');
            history.replaceState(null, '', url);
        }

        document.querySelector('[data-open-editor]').addEventListener('click', openEditor);

        /* ---------------- ACCIONES DEL EDITOR ---------------- */

        editor.addEventListener('input', event => {
            if (event.target.id === 'editName') {
                state.name = event.target.value;
                title.textContent = state.name.trim() || originalTitle;
                updateStatus();
            }
        });

        editor.addEventListener('change', event => {
            const target = event.target;

            if (target.dataset.action === 'day-change') {
                state.days[Number(target.dataset.day)].day = target.value;
                render();
            }
        });

        editor.addEventListener('click', event => {
            const button = event.target.closest('[data-action]');
            if (!button || button.tagName === 'SELECT') return;

            const action = button.dataset.action;
            const i = Number(button.dataset.day);
            const j = Number(button.dataset.ex);
            const day = state.days[i];

            if (action === 'up' && j > 0) {
                const id = day.exercises[j];
                [day.exercises[j - 1], day.exercises[j]] = [day.exercises[j], day.exercises[j - 1]];
                render(j - 1 === 0 ? `down-${i}-${id}` : `up-${i}-${id}`);
            }

            if (action === 'down' && j < day.exercises.length - 1) {
                const id = day.exercises[j];
                [day.exercises[j + 1], day.exercises[j]] = [day.exercises[j], day.exercises[j + 1]];
                render(j + 1 === day.exercises.length - 1 ? `up-${i}-${id}` : `down-${i}-${id}`);
            }

            if (action === 'remove-ex') {
                day.exercises.splice(j, 1);
                render(`add-${i}`);
            }

            if (action === 'remove-day') {
                const name = config.dayNames[day.day];

                if (window.confirm(`¿Quitar el ${name.toLowerCase()} de la rutina?`)) {
                    state.days.splice(i, 1);
                    render('add-day');
                }
            }

            if (action === 'add-day') {
                const used = state.days.map(d => d.day);
                const free = DAY_ORDER.find(key => !used.includes(key));
                if (!free) return;

                state.days.push({ id: null, day: free, exercises: [] });
                state.days.sort((a, b) => DAY_ORDER.indexOf(a.day) - DAY_ORDER.indexOf(b.day));
                render();
                openPicker(state.days.findIndex(d => d.day === free));
            }

            if (action === 'add-ex') {
                openPicker(i);
            }

            if (action === 'cancel') {
                if (!isDirty() || window.confirm('¿Descartar los cambios sin guardar?')) {
                    closeEditor();
                }
            }

            if (action === 'save') {
                save();
            }
        });

        /* ---------------- GUARDAR ---------------- */

        function save() {
            const error = editor.querySelector('#editError');
            const name = state.name.trim();

            if (!name) {
                error.textContent = 'Escribe un nombre para la rutina.';
                editor.querySelector('#editName').focus();
                return;
            }

            const emptyDay = state.days.find(d => d.exercises.length === 0);

            if (emptyDay) {
                error.textContent = `El ${config.dayNames[emptyDay.day].toLowerCase()} no tiene ejercicios. Añade alguno o quita el día.`;
                return;
            }

            const fields = [];
            const hidden = (fieldName, value) =>
                fields.push(`<input type="hidden" name="${esc(fieldName)}" value="${esc(value)}">`);

            hidden('name', name);

            state.days.forEach((day, i) => {
                hidden(`days[${i}][id]`, day.id ?? '');
                hidden(`days[${i}][day]`, day.day);
                day.exercises.forEach(id => hidden(`days[${i}][exercises][]`, id));
            });

            updateFields.innerHTML = fields.join('');
            saving = true;

            editor.querySelector('[data-action="save"]').disabled = true;
            updateForm.submit();
        }

        window.addEventListener('beforeunload', event => {
            if (!editor.hidden && isDirty() && !saving) {
                event.preventDefault();
                event.returnValue = '';
            }
        });

        /* ---------------- SELECTOR DE EJERCICIOS ---------------- */

        [...new Set(config.catalog.map(ex => ex.group))].forEach(group => {
            pickerGroup.insertAdjacentHTML('beforeend', `<option value="${esc(group)}">${esc(group)}</option>`);
        });

        pickerGrid.innerHTML = config.catalog.map(ex => `
            <button type="button" class="tf-picker-item" aria-pressed="false"
                data-id="${esc(ex.id)}" data-name="${esc(normalize(ex.name))}" data-group="${esc(ex.group)}">
                <img src="${esc(ex.img)}" alt="" loading="lazy">
                <span class="tf-picker-item-name">${esc(ex.name)}</span>
                <span class="tf-picker-item-group">${esc(ex.group)}</span>
            </button>`).join('');

        const pickerItems = [...pickerGrid.querySelectorAll('.tf-picker-item')];

        function filterPicker() {
            const query = normalize(pickerSearch.value);
            const group = pickerGroup.value;
            let visible = 0;

            pickerItems.forEach(item => {
                const match = item.dataset.name.includes(query)
                    && (!group || item.dataset.group === group);

                item.hidden = !match;
                if (match) visible++;
            });

            pickerEmpty.hidden = visible > 0;
        }

        function syncPicker() {
            const selected = state.days[pickerDayIndex]?.exercises ?? [];

            pickerItems.forEach(item => {
                item.setAttribute('aria-pressed', String(selected.includes(item.dataset.id)));
            });

            pickerCount.textContent = plural(selected.length, 'ejercicio en este día', 'ejercicios en este día');
        }

        function openPicker(dayIndex) {
            pickerDayIndex = dayIndex;
            pickerDay.textContent = config.dayNames[state.days[dayIndex].day];
            pickerSearch.value = '';
            pickerGroup.value = '';
            filterPicker();
            syncPicker();
            picker.showModal();
            pickerSearch.focus();
        }

        pickerGrid.addEventListener('click', event => {
            const item = event.target.closest('.tf-picker-item');
            if (!item || pickerDayIndex === null) return;

            const list = state.days[pickerDayIndex].exercises;
            const index = list.indexOf(item.dataset.id);

            if (index === -1) {
                list.push(item.dataset.id);
            } else {
                list.splice(index, 1);
            }

            syncPicker();
        });

        pickerSearch.addEventListener('input', filterPicker);
        pickerGroup.addEventListener('change', filterPicker);

        pickerSearch.addEventListener('keydown', event => {
            if (event.key === 'Enter') event.preventDefault();
        });

        picker.querySelectorAll('[data-picker-close]').forEach(button => {
            button.addEventListener('click', () => picker.close());
        });

        picker.addEventListener('close', () => {
            const index = pickerDayIndex;
            pickerDayIndex = null;
            render(index !== null ? `add-${index}` : null);
        });

        /* ---------------- ELIMINAR RUTINA ---------------- */

        document.querySelector('[data-open-delete]').addEventListener('click', () => {
            deleteDialog.showModal();
        });

        deleteDialog.querySelector('[data-delete-close]').addEventListener('click', () => {
            deleteDialog.close();
        });

        /* Cerrar diálogos al pulsar fuera */
        [picker, deleteDialog].forEach(dialog => {
            dialog.addEventListener('click', event => {
                if (event.target === dialog) dialog.close();
            });
        });

        if (config.openEditor) {
            openEditor();
        }
    })();
    </script>

    <script>
        (() => {
            const images = document.querySelectorAll('.tf-routine-follow-page .tf-routine-ex-img');
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

            if (images.length) {
                let secondFrame = false;

                window.setInterval(() => {
                    if (document.hidden) return;

                    secondFrame = reducedMotion.matches ? false : !secondFrame;

                    images.forEach(image => {
                        const next = secondFrame ? image.dataset.frame2 : image.dataset.frame1;

                        if (next && image.getAttribute('src') !== next) {
                            image.src = next;
                        }
                    });
                }, 850);
            }

            document.querySelectorAll('[data-tf-toast]').forEach(toast => {
                window.setTimeout(() => {
                    toast.classList.add('tf-toast-hide');
                    window.setTimeout(() => toast.remove(), 300);
                }, 4500);
            });
        })();
    </script>
</x-app-layout>