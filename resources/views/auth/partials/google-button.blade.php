@once
<style>
    .tf-social-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 22px 0 16px;
        color: var(--muted, #b8b8c7);
        font-size: 12px;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .tf-social-divider::before,
    .tf-social-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: rgba(124, 58, 237, .35);
    }

    .tf-google-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 46px;
        padding: 11px 16px;
        border: 1px solid #3a3a46;
        border-radius: 14px;
        background: #fff;
        color: #1f1f1f;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        box-sizing: border-box;
        transition: background .2s;
    }

    .tf-google-btn:hover {
        background: #f1f1f4;
    }

    .tf-google-btn svg {
        flex: 0 0 18px;
    }
</style>
@endonce

<a class="tf-google-btn" href="{{ route('social.redirect', 'google') }}">
    <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/>
        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
        <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/>
    </svg>
    <span>Continuar con Google</span>
</a>
