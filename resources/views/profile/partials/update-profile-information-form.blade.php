<section aria-labelledby="profile-information-title">
    <header>
        <h2 class="pf-section-title" id="profile-information-title">
            Información personal
        </h2>

        <p class="pf-section-description">
            Actualiza tu nombre y la dirección de correo de tu cuenta.
        </p>
    </header>

    <form
        id="send-verification"
        method="POST"
        action="{{ route('verification.send') }}"
    >
        @csrf
    </form>

    <form
        method="POST"
        action="{{ route('profile.update') }}"
        class="pf-form"
    >
        @csrf
        @method('PATCH')

        @if (session('status') === 'profile-updated')
            <p class="pf-notice pf-success" role="status">
                Tus datos se han actualizado correctamente.
            </p>
        @endif

        <div class="pf-grid">
            <div class="pf-field">
                <label class="pf-label" for="profile-name">
                    Nombre
                </label>

                <input
                    class="pf-input"
                    id="profile-name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    autocomplete="name"
                    maxlength="255"
                    required
                    @if ($errors->has('name'))
                        aria-invalid="true"
                        aria-describedby="profile-name-errors"
                    @endif
                >

                @if ($errors->has('name'))
                    <div id="profile-name-errors">
                        @foreach ($errors->get('name') as $error)
                            <p class="pf-error">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pf-field">
                <label class="pf-label" for="profile-email">
                    Correo electrónico
                </label>

                <input
                    class="pf-input"
                    id="profile-email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    autocomplete="email"
                    required
                    aria-describedby="profile-email-help{{ $errors->has('email') ? ' profile-email-errors' : '' }}"
                    @if ($errors->has('email'))
                        aria-invalid="true"
                    @endif
                >

                <p class="pf-help" id="profile-email-help">
                    Si cambias tu correo, tendrás que verificar
                    la nueva dirección.
                </p>

                @if ($errors->has('email'))
                    <div id="profile-email-errors">
                        @foreach ($errors->get('email') as $error)
                            <p class="pf-error">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if (
            $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
            && ! $user->hasVerifiedEmail()
        )
            <div class="pf-notice">
                <p>Tu correo todavía no está verificado.</p>

                <button
                    type="submit"
                    form="send-verification"
                    class="pf-text-button"
                >
                    Reenviar enlace de verificación
                </button>
            </div>

            @if (session('status') === 'verification-link-sent')
                <p class="pf-notice pf-success" role="status">
                    Hemos enviado otro enlace. Revisa tu correo
                    y la carpeta de spam.
                </p>
            @endif
        @endif

        <div class="pf-actions">
            <button class="pf-button" type="submit">
                Guardar cambios
            </button>
        </div>
    </form>
</section>