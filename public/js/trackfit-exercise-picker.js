(() => {
    function initializeExercisePicker() {
        const select = document.getElementById('exerciseSelect');

        if (!select || select.dataset.enhanced === 'true') return;

        select.dataset.enhanced = 'true';

        const items = Array.from(select.options)
            .filter(option => option.value !== '')
            .map(option => ({
                value: option.value,
                label: option.textContent.trim(),
            }));

        const normalize = value => value
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('es')
            .trim();

        const picker = document.createElement('div');
        picker.className = 'ep-picker';

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.id = 'exercisePickerTrigger';
        trigger.className = 'ep-trigger';
        trigger.setAttribute('aria-expanded', 'false');
        trigger.setAttribute('aria-controls', 'exercisePickerPanel');
        trigger.setAttribute('aria-haspopup', 'dialog');

        const triggerLabel = document.createElement('span');
        triggerLabel.className = 'ep-trigger-label';

        const arrow = document.createElement('span');
        arrow.className = 'ep-arrow';
        arrow.textContent = '⌄';
        arrow.setAttribute('aria-hidden', 'true');

        trigger.append(triggerLabel, arrow);

        const panel = document.createElement('div');
        panel.id = 'exercisePickerPanel';
        panel.className = 'ep-panel';
        panel.hidden = true;
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-label', 'Elegir ejercicio');

        const search = document.createElement('input');
        search.type = 'search';
        search.className = 'ep-search';
        search.placeholder = 'Buscar ejercicio…';
        search.autocomplete = 'off';
        search.setAttribute('role', 'combobox');
        search.setAttribute('aria-label', 'Buscar ejercicio');
        search.setAttribute('aria-autocomplete', 'list');
        search.setAttribute('aria-controls', 'exercisePickerList');
        search.setAttribute('aria-expanded', 'false');

        const list = document.createElement('ul');
        list.id = 'exercisePickerList';
        list.className = 'ep-list';
        list.setAttribute('role', 'listbox');
        list.setAttribute('aria-label', 'Ejercicios');

        const error = document.createElement('p');
        error.className = 'ep-error';
        error.hidden = true;
        error.setAttribute('role', 'alert');

        panel.append(search, list);
        picker.append(trigger, panel, error);
        select.insertAdjacentElement('afterend', picker);

        // El select conserva name, value y su validación.
        select.hidden = true;

        const label = document.querySelector(
            'label[for="exerciseSelect"]'
        );

        if (label) {
            label.htmlFor = trigger.id;
        }

        let matches = [];
        let activeIndex = -1;

        function close(restoreFocus = false) {
            panel.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            search.setAttribute('aria-expanded', 'false');
            search.removeAttribute('aria-activedescendant');

            if (restoreFocus && !trigger.disabled) {
                trigger.focus();
            }
        }

        function activate(index, scroll = true) {
            activeIndex = index;

            const options = list.querySelectorAll('[role="option"]');

            options.forEach((option, position) => {
                option.classList.toggle(
                    'ep-active',
                    position === index
                );
            });

            const current = options[index];

            if (current) {
                search.setAttribute(
                    'aria-activedescendant',
                    current.id
                );

                if (scroll) {
                    current.scrollIntoView({ block: 'nearest' });
                }
            } else {
                search.removeAttribute('aria-activedescendant');
            }
        }

        function choose(index) {
            const item = matches[index];

            if (!item) return;

            select.value = item.value;
            error.hidden = true;

            select.dispatchEvent(
                new Event('change', { bubbles: true })
            );

            sync();
            close(true);
        }

        function render() {
            const query = normalize(search.value);

            matches = items.filter(item =>
                normalize(item.label).includes(query)
            );

            list.replaceChildren();
            activeIndex = -1;
            search.removeAttribute('aria-activedescendant');

            if (!matches.length) {
                const empty = document.createElement('li');

                empty.className = 'ep-empty';
                empty.setAttribute('role', 'presentation');
                empty.textContent =
                    'No encontramos ejercicios con ese nombre.';

                list.appendChild(empty);
                return;
            }

            matches.forEach((item, index) => {
                const option = document.createElement('li');

                option.id = `exercise-picker-option-${index}`;
                option.className = 'ep-option';
                option.textContent = item.label;
                option.setAttribute('role', 'option');
                option.setAttribute(
                    'aria-selected',
                    select.value === item.value ? 'true' : 'false'
                );

                option.addEventListener('pointerenter', () => {
                    activate(index, false);
                });

                option.addEventListener('click', () => {
                    choose(index);
                });

                list.appendChild(option);
            });
        }

        function open() {
            if (select.disabled) return;

            search.value = '';
            render();

            panel.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            search.setAttribute('aria-expanded', 'true');
            search.focus();

            const selectedIndex = matches.findIndex(
                item => item.value === select.value
            );

            if (selectedIndex >= 0) {
                activate(selectedIndex);
            }
        }

        function sync() {
            const selected = items.find(
                item => item.value === select.value
            );

            triggerLabel.textContent =
                selected?.label || 'Selecciona un ejercicio';

            trigger.disabled = select.disabled;

            if (select.disabled) {
                error.hidden = true;
                close();
            }
        }

        trigger.addEventListener('click', () => {
            if (panel.hidden) {
                open();
            } else {
                close(true);
            }
        });

        trigger.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                open();
            }
        });

        search.addEventListener('input', render);

        search.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();

                if (!matches.length) return;

                const next = event.key === 'ArrowDown'
                    ? (activeIndex + 1) % matches.length
                    : (activeIndex <= 0
                        ? matches.length - 1
                        : activeIndex - 1);

                activate(next);
            }

            if (event.key === 'Enter') {
                event.preventDefault();

                if (activeIndex >= 0) {
                    choose(activeIndex);
                } else if (matches.length === 1) {
                    choose(0);
                }
            }
        });

        picker.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !panel.hidden) {
                event.preventDefault();
                event.stopPropagation();
                close(true);
            }
        });

        list.addEventListener('mousedown', event => {
            event.preventDefault();
        });

        picker.addEventListener('focusout', event => {
            if (!picker.contains(event.relatedTarget)) {
                close();
            }
        });

        document.addEventListener('click', event => {
            if (!picker.contains(event.target)) {
                close();
            }
        });

        select.addEventListener('change', sync);

        // Redirigir la validación del select oculto al selector visible.
        select.addEventListener('invalid', event => {
            event.preventDefault();

            error.textContent =
                'Selecciona un ejercicio antes de buscar.';
            error.hidden = false;

            open();
        });

        // Mantener el estado desactivado del modo general.
        const observer = new MutationObserver(sync);

        observer.observe(select, {
            attributes: true,
            attributeFilter: ['disabled'],
        });

        sync();
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initializeExercisePicker,
            { once: true }
        );
    } else {
        initializeExercisePicker();
    }
})();