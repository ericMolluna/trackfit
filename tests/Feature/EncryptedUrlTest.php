<?php

namespace Tests\Feature;

use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncryptedUrlTest extends TestCase
{
    use RefreshDatabase;

    private function routineFor(User $user): Routine
    {
        $routine = Routine::create(['user_id' => $user->id, 'name' => 'Full body']);
        RoutineDay::create(['routine_id' => $routine->id, 'day' => 'mon', 'position' => 1]);

        return $routine;
    }

    public function test_urls_do_not_contain_the_numeric_id(): void
    {
        $user = User::factory()->create();
        $routine = $this->routineFor($user);

        $url = route('rutinas.edit', $routine);

        $this->assertStringNotContainsString('/rutinas/'.$routine->id.'/', $url);
        $this->assertGreaterThan(30, strlen($routine->getRouteKey()));
        $this->assertSame($routine->id, Routine::decodeRouteKey($routine->getRouteKey()));
    }

    public function test_encrypted_url_works(): void
    {
        $user = User::factory()->create();
        $routine = $this->routineFor($user);
        $day = $routine->days()->first();

        $this->actingAs($user)->get(route('rutinas.edit', $routine))->assertRedirect();
        $this->actingAs($user)->get(route('rutina.follow', ['routine' => $routine]))->assertOk()->assertSee('Full body');
        $this->actingAs($user)->get(route('workout.show', $day))->assertOk();
    }

    public function test_numeric_or_modified_urls_return_404(): void
    {
        $user = User::factory()->create();
        $routine = $this->routineFor($user);
        $day = $routine->days()->first();
        $key = $routine->getRouteKey();
        $tampered = substr($key, 0, -2).($key[-2] === 'A' ? 'B' : 'A').$key[-1];

        $this->actingAs($user)->get('/rutinas/'.$routine->id.'/editar')->assertNotFound();
        $this->actingAs($user)->get('/rutinas/'.$tampered.'/editar')->assertNotFound();
        $this->actingAs($user)->get('/rutina/seguir?routine='.$routine->id)->assertNotFound();
        $this->actingAs($user)->get('/rutina/seguir?routine='.$tampered)->assertNotFound();
        $this->actingAs($user)->get('/rutina/entreno/'.$day->id)->assertNotFound();
        $this->actingAs($user)->delete('/rutinas/'.$routine->id)->assertNotFound();

        // La clave de una rutina no sirve como clave de un día.
        $this->actingAs($user)->get('/rutina/entreno/'.$key)->assertNotFound();

        $this->assertNotNull($routine->fresh());
    }

    public function test_other_users_encrypted_url_is_rejected(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $routine = $this->routineFor($owner);
        $this->routineFor($other);

        $this->actingAs($other)->get(route('rutinas.edit', $routine))->assertForbidden();
        $this->actingAs($other)->get(route('rutina.follow', ['routine' => $routine]))->assertNotFound();
    }
}
