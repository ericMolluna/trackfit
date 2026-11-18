<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\PasswordRules;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // Las cuentas de Google o GitHub no usan contraseña.
        abort_if(filled($request->user()->provider), 403);

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            // Mismas reglas que al crear la cuenta (app/Support/PasswordRules.php).
            'password' => ['required', PasswordRules::rule(), 'confirmed'],
        ], [
            'current_password.required' => 'Introduce tu contraseña actual.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
        ] + PasswordRules::messages());

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
