<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifica tu correo - TrackFit</title>
</head>

<body style="margin:0; padding:0; background-color:#101014; font-family:Arial,Helvetica,sans-serif; color:#f5f3ff;">
    <table
        role="presentation"
        width="100%"
        cellspacing="0"
        cellpadding="0"
        border="0"
        style="background-color:#101014;"
    >
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table
                    role="presentation"
                    width="100%"
                    cellspacing="0"
                    cellpadding="0"
                    border="0"
                    style="max-width:560px; background-color:#19171f; border:1px solid #6940a0; border-radius:20px;"
                >
                    <tr>
                        <td style="padding:32px 28px 24px; border-bottom:1px solid #342b43;">
                            <p style="margin:0; color:#ffffff; font-size:30px; font-weight:700;">
                                Track<span style="color:#a78bfa;">Fit</span>
                            </p>

                            <p style="margin:8px 0 0; color:#bdb4ca; font-size:14px;">
                                Tu progreso empieza aquí
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 28px;">
                            <h1 style="margin:0 0 18px; color:#ffffff; font-size:25px; line-height:1.3;">
                                ¡Bienvenido, {{ $name }}!
                            </h1>

                            <p style="margin:0 0 16px; color:#d6d0df; font-size:15px; line-height:1.7;">
                                Tu cuenta de TrackFit se ha creado.
                                Solo falta confirmar que este correo es tuyo.
                            </p>

                            <p style="margin:0 0 26px; color:#d6d0df; font-size:15px; line-height:1.7;">
                                Pulsa el botón para verificar tu correo y continuar
                                con tus entrenamientos, tu alimentación y tu progreso.
                            </p>

                            <table
                                role="presentation"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                style="margin:0 0 26px;"
                            >
                                <tr>
                                    <td
                                        align="center"
                                        bgcolor="#7c3aed"
                                        style="border-radius:12px;"
                                    >
                                        <a
                                            href="{{ $verificationUrl }}"
                                            style="display:inline-block; padding:16px 26px; color:#ffffff; font-size:15px; font-weight:700; text-decoration:none;"
                                        >
                                            Verificar mi correo
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 18px; color:#bdb4ca; font-size:13px; line-height:1.7;">
                                Este enlace caduca en {{ $expiresIn }} minutos.
                                Si caduca, puedes solicitar uno nuevo desde TrackFit.
                            </p>

                            <p style="margin:0; color:#bdb4ca; font-size:13px; line-height:1.7;">
                                Si no has creado esta cuenta, puedes ignorar este mensaje.
                                No necesitas pulsar el botón.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 28px; border-top:1px solid #342b43;">
                            <p style="margin:0 0 10px; color:#a99db9; font-size:12px; line-height:1.6;">
                                Si el botón no funciona, copia y pega este enlace en el navegador:
                            </p>

                            <a
                                href="{{ $verificationUrl }}"
                                style="color:#c4b5fd; font-size:12px; line-height:1.6; word-break:break-all;"
                            >
                                {{ $verificationUrl }}
                            </a>

                            <p style="margin:24px 0 0; color:#a99db9; font-size:12px;">
                                © {{ date('Y') }} TrackFit
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>