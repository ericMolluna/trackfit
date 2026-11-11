import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Alpine debe iniciarse una sola vez.
Alpine.start();

function initializeWelcomePage() {
    const btn = document.getElementById('enterBtn');
    const hero = document.getElementById('heroClick');
    const loginUrl = hero?.dataset?.loginUrl || '/login';

    // Botón de acceso.
    btn?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        window.location.href = loginUrl;
    });

    // Bloque central de acceso.
    hero?.addEventListener('click', () => {
        window.location.href = loginUrl;
    });

    // Elementos de la animación.
    const logo = document.getElementById('logo');
    const brand = document.getElementById('brandType');
    const sub = document.getElementById('subType');

    function setupTyping(element, durationMs) {
        if (!element) return;

        const text = element.dataset.text || '';

        element.textContent = text;

        const measurer = document.createElement('span');

        measurer.textContent = text;
        measurer.style.position = 'absolute';
        measurer.style.visibility = 'hidden';
        measurer.style.whiteSpace = 'nowrap';

        const computedStyle = window.getComputedStyle(element);

        measurer.style.font = computedStyle.font;
        measurer.style.letterSpacing = computedStyle.letterSpacing;
        measurer.style.textTransform = computedStyle.textTransform;

        document.body.appendChild(measurer);

        const width = Math.ceil(
            measurer.getBoundingClientRect().width
        );

        measurer.remove();

        element.style.setProperty('--w', `${width}px`);
        element.style.setProperty(
            '--steps',
            `${Math.max(8, text.length)}`
        );
        element.style.setProperty(
            '--dur',
            `${durationMs / 1000}s`
        );
    }

    setupTyping(brand, 850);
    setupTyping(sub, 1100);

    logo?.addEventListener(
        'animationend',
        () => {
            brand?.classList.add('typing');

            setTimeout(() => {
                sub?.classList.add('typing');
            }, 950);

            setTimeout(() => {
                btn?.classList.add('show');
            }, 2100);
        },
        { once: true }
    );
}

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        initializeWelcomePage,
        { once: true }
    );
} else {
    initializeWelcomePage();
}