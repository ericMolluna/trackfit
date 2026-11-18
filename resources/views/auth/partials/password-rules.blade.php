{{--
    Lista de requisitos de la contraseña que se marca en verde mientras se escribe.
    Uso: @include('auth.partials.password-rules', ['for' => 'id-del-input'])
    Las mismas reglas se comprueban en el servidor: app/Support/PasswordRules.php
--}}
@once
    <style>
        /* Requisitos de la contraseña */
        .tf-password-rules {
            margin: 10px 0 4px;
            padding: 12px 14px;
            border: 1px solid #3c2c51;
            border-radius: 12px;
            background: #1b1524;
        }

        .tf-strength {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .tf-strength-bar {
            flex: 1;
            height: 6px;
            border-radius: 99px;
            background: #2d2340;
            overflow: hidden;
        }

        .tf-strength-fill {
            width: 0;
            height: 100%;
            border-radius: 99px;
            background: #fb7185;
            transition: width .2s, background .2s;
        }

        .tf-strength-text { min-width: 140px; text-align: right; color: #fda4af; }
        .tf-strength--medium .tf-strength-fill { background: #fbbf24; }
        .tf-strength--medium .tf-strength-text { color: #fcd34d; }
        .tf-strength--strong .tf-strength-fill { background: #34d399; }
        .tf-strength--strong .tf-strength-text { color: #6ee7b7; }

        .tf-password-rules ul {
            display: grid;
            gap: 4px;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 13px;
            line-height: 1.5;
            color: #b8b0c6;
        }

        .tf-password-rules li::before {
            content: "✕";
            display: inline-block;
            width: 18px;
            color: #fb7185;
        }

        .tf-password-rules li.ok { color: #d1fae5; }
        .tf-password-rules li.ok::before { content: "✓"; color: #34d399; }
    </style>

    <script>
        // Comprueba los requisitos de cada lista en cuanto se escribe.
        document.addEventListener('DOMContentLoaded', function () {
            const rules = {
                length: (v) => v.length >= 8,
                upper: (v) => /\p{Lu}/u.test(v),
                lower: (v) => /\p{Ll}/u.test(v),
                number: (v) => /\p{N}/u.test(v),
            };

            document.querySelectorAll('[data-password-rules]').forEach(function (box) {
                const input = document.getElementById(box.dataset.passwordRules);
                const strength = box.querySelector('.tf-strength');
                const fill = box.querySelector('.tf-strength-fill');
                const text = box.querySelector('.tf-strength-text');

                if (! input) return;

                function update() {
                    let passed = 0;

                    for (const [name, check] of Object.entries(rules)) {
                        const ok = check(input.value);
                        box.querySelector('[data-rule="' + name + '"]').classList.toggle('ok', ok);
                        if (ok) passed++;
                    }

                    strength.classList.toggle('tf-strength--medium', passed >= 2 && passed < 4);
                    strength.classList.toggle('tf-strength--strong', passed === 4);
                    text.textContent = passed === 4 ? 'Contraseña segura' : 'Contraseña poco segura';
                    fill.style.width = (passed / 4 * 100) + '%';
                }

                input.addEventListener('input', update);
                update();
            });
        });
    </script>
@endonce

<div class="tf-password-rules" id="password-rules-{{ $for }}" data-password-rules="{{ $for }}">
    <div class="tf-strength">
        <div class="tf-strength-bar">
            <div class="tf-strength-fill"></div>
        </div>
        <span class="tf-strength-text" aria-live="polite">Contraseña poco segura</span>
    </div>

    <ul>
        <li data-rule="length">Mínimo 8 caracteres</li>
        <li data-rule="upper">Una letra mayúscula</li>
        <li data-rule="lower">Una letra minúscula</li>
        <li data-rule="number">Un número</li>
    </ul>
</div>
