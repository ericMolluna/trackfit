<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('Mínimo 8 caracteres');
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Segura123',
        'password_confirmation' => 'Segura123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice', absolute: false));
    $this->get(route('verification.notice'))->assertDontSee('Esta cuenta no está verificada');
});

test('weak passwords are rejected with clear messages', function (string $password, string $message) {
    $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'weak@example.com',
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertSessionHasErrors(['password' => $message]);

    $this->assertGuest();
})->with([
    ['Ab1', 'Contraseña poco segura: debe tener al menos 8 caracteres.'],
    ['segura123', 'Contraseña poco segura: necesita al menos una mayúscula y una minúscula.'],
    ['SeguraSegura', 'Contraseña poco segura: necesita al menos un número.'],
]);

test('google linked email shows a specific message', function () {
    User::factory()->create([
        'email' => 'google@example.com',
        'provider' => 'google',
        'provider_id' => '123',
        'password' => null,
    ]);

    $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'google@example.com',
        'password' => 'Segura123',
        'password_confirmation' => 'Segura123',
    ])->assertSessionHasErrors(['email' => 'Esta cuenta ya está vinculada con Google.']);

    $this->followingRedirects()->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'google@example.com',
        'password' => 'Segura123',
        'password_confirmation' => 'Segura123',
    ])->assertSee('Esta cuenta ya está vinculada con Google')->assertSee('Continuar con Google');
});

test('unverified users opening a private url see a notice', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('verification.notice'));

    $this->actingAs($user)->followingRedirects()->get('/dashboard')
        ->assertSee('Esta cuenta no está verificada');
});
