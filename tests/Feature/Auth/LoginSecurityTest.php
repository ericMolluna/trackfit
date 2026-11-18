<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('login ignores spaces and capital letters in the email', function () {
    $user = User::factory()->create(['email' => 'eric@example.com']);

    $this->post('/login', ['email' => '  Eric@Example.COM ', 'password' => 'password'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('register stores the email in lowercase', function () {
    $this->post('/register', [
        'name' => 'Eric',
        'email' => ' Nuevo@Example.COM ',
        'password' => 'Segura123',
        'password_confirmation' => 'Segura123',
    ]);

    expect(User::where('email', 'nuevo@example.com')->exists())->toBeTrue();
});

test('login is blocked after five failed attempts', function () {
    User::factory()->create(['email' => 'eric@example.com']);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', ['email' => 'eric@example.com', 'password' => 'mala']);
    }

    $this->post('/login', ['email' => 'eric@example.com', 'password' => 'password'])
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('logout closes the session', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/logout')->assertRedirect('/');
    $this->assertGuest();
    $this->get('/dashboard')->assertRedirect('/login');
});

test('private pages send security headers and are not cached', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/profile');

    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    expect($response->headers->get('Cache-Control'))->toContain('no-store');
});

test('changing the password closes the other sessions', function () {
    $user = User::factory()->create();

    // Sesión "de otro dispositivo" con el hash de la contraseña antigua.
    $this->actingAs($user)
        ->withSession(['password_hash_web' => $user->getAuthPassword()])
        ->get('/profile')->assertOk();

    $user->forceFill(['password' => Hash::make('Nueva12345')])->save();

    $this->actingAs($user->fresh())
        ->withSession(['password_hash_web' => 'hash-antiguo'])
        ->get('/profile')
        ->assertRedirect('/login');
});

test('reset password uses the same strong rules', function () {
    $user = User::factory()->create();
    $token = \Illuminate\Support\Facades\Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'debil',
        'password_confirmation' => 'debil',
    ])->assertSessionHasErrors('password');

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'Segura123',
        'password_confirmation' => 'Segura123',
    ])->assertRedirect(route('login'));
});

test('changing the email sends a new verification link', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', [
        'name' => $user->name,
        'email' => 'otro@example.com',
    ]);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user->fresh(), VerifyEmail::class);
});

test('expired forms go back with a friendly message instead of error 419', function () {
    // En las pruebas Laravel no comprueba el token CSRF, así que simulamos el 419.
    \Illuminate\Support\Facades\Route::middleware('web')->post('/_prueba-419', fn () => abort(419));

    $this->from('/login')->post('/_prueba-419')
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['session' => 'La página ha caducado por seguridad. Vuelve a intentarlo.']);
});

test('too many attempts go back with a friendly message instead of error 429', function () {
    for ($i = 0; $i < 10; $i++) {
        $this->from('/register')->post('/register', ['email' => 'x']);
    }

    $this->from('/register')->post('/register', ['email' => 'x'])
        ->assertRedirect('/register')
        ->assertSessionHasErrors('session');
});
