<style>
    .tf-delete-section {
        text-align: left;
    }

    .tf-delete-section .tf-delete-heading {
        margin: 0 0 10px;
        color: #fff;
        font-size: 23px;
        font-weight: 700;
        line-height: 1.3;
    }

    .tf-delete-section .tf-delete-description {
        max-width: 680px;
        margin: 0 0 22px;
        color: #bdb5c8;
        font-size: 14px;
        line-height: 1.7;
    }

    .tf-delete-section .tf-delete-open,
    .tf-delete-dialog .tf-delete-confirm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 12px 18px;
        border: 1px solid #b83a50;
        border-radius: 11px;
        background: #a8233c;
        color: #fff;
        font: inherit;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.4;
        text-transform: none;
        letter-spacing: normal;
        cursor: pointer;
        transition: background 0.2s;
    }

    .tf-delete-section .tf-delete-open:hover,
    .tf-delete-dialog .tf-delete-confirm:hover {
        background: #c52b48;
    }

    dialog.tf-delete-dialog {
        position: fixed;
        inset: 0;
        width: min(520px, calc(100% - 32px));
        max-width: 520px;
        max-height: calc(100dvh - 40px);
        margin: auto;
        padding: 0;
        overflow-y: auto;
        border: 1px solid #683341;
        border-radius: 20px;
        background: #121015;
        color: #f6f3fa;
        text-align: left;
        color-scheme: dark;
        box-shadow: 0 24px 90px rgba(0, 0, 0, 0.65);
    }

    dialog.tf-delete-dialog:not([open]) {
        display: none;
    }

    dialog.tf-delete-dialog::backdrop {
        background: rgba(0, 0, 0, 0.78);
        backdrop-filter: blur(5px);
    }

    .tf-delete-dialog .tf-delete-form {
        margin: 0;
        padding: 30px;
    }

    .tf-delete-dialog .tf-delete-badge {
        display: inline-block;
        margin-bottom: 18px;
        padding: 7px 11px;
        border: 1px solid #713342;
        border-radius: 8px;
        background: #30151e;
        color: #fda4af;
        font-size: 12px;
        font-weight: 700;
    }

    .tf-delete-dialog .tf-delete-title {
        margin: 0 0 14px;
        color: #fff;
        font-size: 25px;
        font-weight: 700;
        line-height: 1.3;
    }

    .tf-delete-dialog .tf-delete-text {
        margin: 0;
        color: #c7bdcf;
        font-size: 14px;
        line-height: 1.7;
    }

    .tf-delete-dialog .tf-delete-warning {
        margin: 18px 0 24px;
        padding: 13px 15px;
        border: 1px solid #60303d;
        border-radius: 11px;
        background: #29151c;
        color: #fecdd3;
        font-size: 13px;
        line-height: 1.6;
    }

    .tf-delete-dialog .tf-delete-label {
        display: block;
        margin: 0 0 9px;
        color: #e5dceb;
        font-size: 13px;
        font-weight: 700;
    }

    .tf-delete-dialog .tf-delete-input {
        display: block;
        width: 100%;
        min-height: 46px;
        margin: 0;
        padding: 12px 15px;
        border: 1px solid #68507d;
        border-radius: 11px;
        background: #08070a;
        color: #fff;
        font: inherit;
        font-size: 15px;
        box-sizing: border-box;
    }

    .tf-delete-dialog .tf-delete-input::placeholder {
        color: #95899f;
    }

    .tf-delete-dialog .tf-delete-input:focus {
        border-color: #a78bfa;
        outline: 2px solid rgba(167, 139, 250, 0.25);
        outline-offset: 2px;
    }

    .tf-delete-dialog .tf-delete-input[aria-invalid="true"] {
        border-color: #fb7185;
    }

    .tf-delete-dialog .tf-delete-error {
        margin: 9px 0 0;
        color: #fda4af;
        font-size: 13px;
        line-height: 1.5;
    }

    .tf-delete-dialog .tf-delete-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 26px;
    }

    .tf-delete-dialog .tf-delete-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 12px 18px;
        border: 1px solid #665176;
        border-radius: 11px;
        background: transparent;
        color: #ede6f5;
        font: inherit;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.4;
        text-transform: none;
        letter-spacing: normal;
        cursor: pointer;
    }

    .tf-delete-dialog .tf-delete-cancel:hover {
        background: #281c32;
    }

    .tf-delete-section button:focus-visible,
    .tf-delete-dialog button:focus-visible {
        outline: 2px solid #c4b5fd;
        outline-offset: 4px;
    }

    @media (max-width: 480px) {
        .tf-delete-dialog .tf-delete-form {
            padding: 24px 20px;
        }

        .tf-delete-dialog .tf-delete-actions {
            flex-direction: column;
        }

        .tf-delete-dialog .tf-delete-actions button {
            width: 100%;
        }
    }
</style>

<section
    class="tf-delete-section"
    x-data="{
        openDialog() {
            if (!this.$refs.dialog.open) {
                this.$refs.dialog.showModal();
            }
        },

        closeDialog() {
            this.$refs.dialog.close();
        },

        closeFromBackdrop(event) {
            if (event.target !== this.$refs.dialog) {
                return;
            }

            const rect = this.$refs.dialog.getBoundingClientRect();

            const outside =
                event.clientX < rect.left ||
                event.clientX > rect.right ||
                event.clientY < rect.top ||
                event.clientY > rect.bottom;

            if (outside) {
                this.closeDialog();
            }
        }
    }"
    x-init="
        if (@js($errors->userDeletion->isNotEmpty())) {
            $nextTick(() => openDialog());
        }
    "
>
    <header>
        <h2 class="tf-delete-heading">
            Eliminar cuenta
        </h2>

        <p class="tf-delete-description">
            Eliminar tu cuenta es una acción permanente.
            Antes de continuar, guarda la información que quieras conservar.
        </p>
    </header>

    <button
        type="button"
        class="tf-delete-open"
        x-on:click="openDialog()"
    >
        Eliminar mi cuenta
    </button>

    <dialog
        class="tf-delete-dialog"
        x-ref="dialog"
        aria-labelledby="tf-delete-title"
        aria-describedby="tf-delete-description"
        x-on:click="closeFromBackdrop($event)"
        x-on:close="$refs.password.value = ''"
    >
        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="tf-delete-form"
        >
            @csrf
            @method('DELETE')

            <span class="tf-delete-badge">
                Acción irreversible
            </span>

            <h2 class="tf-delete-title" id="tf-delete-title">
                ¿Quieres eliminar tu cuenta?
            </h2>

            <p class="tf-delete-text" id="tf-delete-description">
                Si continúas, se eliminará tu cuenta de TrackFit.
                Para confirmar la operación, introduce tu contraseña actual.
            </p>

            <p class="tf-delete-warning">
                Esta acción no se puede deshacer.
                Si solo quieres salir de TrackFit, cierra sesión en lugar
                de eliminar tu cuenta.
            </p>

            <label class="tf-delete-label" for="delete-account-password">
                Contraseña actual
            </label>

            <input
                class="tf-delete-input"
                id="delete-account-password"
                x-ref="password"
                name="password"
                type="password"
                autocomplete="current-password"
                placeholder="Introduce tu contraseña"
                required
                @if ($errors->userDeletion->has('password'))
                    aria-invalid="true"
                    aria-describedby="delete-password-error"
                @endif
            >

            @if ($errors->userDeletion->has('password'))
                <p
                    class="tf-delete-error"
                    id="delete-password-error"
                    role="alert"
                >
                    {{ $errors->userDeletion->first('password') }}
                </p>
            @endif

            <div class="tf-delete-actions">
                <button
                    type="button"
                    class="tf-delete-cancel"
                    x-on:click="closeDialog()"
                    autofocus
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="tf-delete-confirm"
                >
                    Eliminar mi cuenta
                </button>
            </div>
        </form>
    </dialog>
</section>