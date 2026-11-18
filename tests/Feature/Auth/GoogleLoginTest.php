<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'test-id',
            'services.google.client_secret' => 'test-secret',
        ]);
    }

    private function fakeGoogle(string $email, string $id = 'g-123', string $name = 'Profe Google', bool $verified = true): void
    {
        $social = (new SocialiteUser())->setRaw(['email_verified' => $verified])->map([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'nickname' => null,
        ]);

        $driver = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $driver->shouldReceive('user')->andReturn($social);

        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);
    }

    public function test_login_and_register_pages_show_google_button(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Continuar con Google');
        $this->get('/login')->assertOk()->assertSee('Continuar con Google')->assertSee('/auth/google/redirect');
        $this->get('/register')->assertOk()->assertSee('Continuar con Google');
    }

    public function test_google_without_verified_email_does_not_create_account(): void
    {
        $this->fakeGoogle('nuevo@gmail.com', verified: false);
        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHasErrors('social');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'nuevo@gmail.com']);
    }

    public function test_google_does_not_match_a_different_provider_id_by_email(): void
    {
        User::factory()->create(['email' => 'nuevo@gmail.com', 'provider' => 'google', 'provider_id' => 'other']);
        $this->fakeGoogle('nuevo@gmail.com');
        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHasErrors('social');
        $this->assertGuest();
    }

    public function test_redirect_goes_to_google(): void
    {
        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect();
        $this->assertStringStartsWith('https://accounts.google.com/', $response->headers->get('Location'));
    }

    public function test_new_google_user_is_created_verified_and_logged_in(): void
    {
        $this->fakeGoogle('nuevo@gmail.com');

        $this->get('/auth/google/callback?code=x&state=y')->assertRedirect('/dashboard');

        $user = User::where('email', 'nuevo@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('google', $user->provider);
        $this->assertNull($user->password);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
    }

    public function test_google_user_cannot_login_with_email_and_password(): void
    {
        $this->fakeGoogle('nuevo@gmail.com');
        $this->get('/auth/google/callback?code=x&state=y');
        $this->post('/logout');

        $this->post('/login', ['email' => 'nuevo@gmail.com', 'password' => 'cualquiera'])
            ->assertSessionHasErrors('social');

        $this->assertGuest();
    }

    public function test_google_user_cannot_request_password_reset(): void
    {
        User::factory()->create(['email' => 'nuevo@gmail.com', 'provider' => 'google', 'provider_id' => 'g-1', 'password' => null]);

        $this->post('/forgot-password', ['email' => 'nuevo@gmail.com'])->assertSessionHasErrors('email');
    }

    public function test_existing_password_account_cannot_be_taken_over_with_google(): void
    {
        $user = User::factory()->create(['email' => 'pau@gmail.com']);
        $hash = $user->password;

        $this->fakeGoogle('pau@gmail.com');

        $this->get('/auth/google/callback?code=x&state=y')
            ->assertRedirect('/login')
            ->assertSessionHasErrors('social');

        $this->assertGuest();
        $this->assertSame($hash, $user->fresh()->password);
        $this->assertNull($user->fresh()->provider);
    }

    public function test_returning_google_user_logs_in_again(): void
    {
        $this->fakeGoogle('nuevo@gmail.com');
        $this->get('/auth/google/callback?code=x&state=y');
        $this->post('/logout');

        $this->get('/auth/google/callback?code=x&state=y')->assertRedirect('/dashboard');

        $this->assertSame(1, User::where('email', 'nuevo@gmail.com')->count());
        $this->assertAuthenticated();
    }

    public function test_google_user_can_delete_account_typing_email(): void
    {
        $user = User::factory()->create(['email' => 'nuevo@gmail.com', 'provider' => 'google', 'provider_id' => 'g-1', 'password' => null]);

        $this->actingAs($user)->delete('/profile', ['confirm_email' => 'otro@gmail.com'])
            ->assertSessionHasErrorsIn('userDeletion', 'confirm_email');
        $this->assertNotNull($user->fresh());

        $this->actingAs($user)->delete('/profile', ['confirm_email' => 'nuevo@gmail.com'])->assertRedirect('/');
        $this->assertNull($user->fresh());
    }

    public function test_google_user_cannot_change_email(): void
    {
        $user = User::factory()->create(['email' => 'nuevo@gmail.com', 'provider' => 'google', 'provider_id' => 'g-1', 'password' => null]);

        $this->actingAs($user)->patch('/profile', ['name' => 'Nuevo nombre', 'email' => 'cambiado@gmail.com']);

        $this->assertSame('nuevo@gmail.com', $user->fresh()->email);
        $this->assertSame('Nuevo nombre', $user->fresh()->name);
    }

    public function test_unverified_user_cannot_open_private_pages_by_url(): void
    {
        $user = User::factory()->unverified()->create();

        foreach (['/dashboard', '/rutina', '/rutina/seguir', '/rutina/crear', '/rutinas', '/rutinas/predefinidas',
                  '/progress', '/progress/records', '/nutrition', '/nutrition/setup', '/profile'] as $url) {
            $this->actingAs($user)->get($url)->assertRedirect('/verify-email');
        }

        $this->actingAs($user)->post('/dashboard/empezar')->assertRedirect('/verify-email');
    }
}
