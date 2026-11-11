<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\Workout;
use App\Models\WorkoutSet;
use Carbon\Carbon;

class WorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('No hay usuarios');
            return;
        }

        // 🔹 TODOS LOS EJERCICIOS
        $exerciseNames = config('exercises.names', []);
        $exerciseIds = array_keys($exerciseNames);

        // 🔹 Rutina base
        $routine = Routine::create([
            'user_id' => $user->id,
            'name' => 'Rutina Demo Completa',
        ]);

        $routineDay = RoutineDay::create([
            'routine_id' => $routine->id,
            'day' => 'mon',
            'position' => 1,
        ]);

        // 🔹 6 semanas → 6 puntos en el gráfico
        $start = Carbon::now()->subWeeks(6)->startOfWeek();

        foreach ($exerciseIds as $exerciseId) {

            // cada ejercicio tendrá sus 6 entrenos
            for ($w = 0; $w < 6; $w++) {

                $date = $start->copy()->addWeeks($w);

                $workout = Workout::create([
                    'user_id' => $user->id,
                    'routine_day_id' => $routineDay->id,
                    'started_at' => $date,
                    'ended_at' => $date->copy()->addHour(),
                    'duration_sec' => 3600,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // progresión simple
                $baseWeight = rand(40, 100);

                for ($set = 1; $set <= 3; $set++) {

                    $weight = $baseWeight + ($w * 2);
                    $reps = rand(6, 10);

                    WorkoutSet::create([
                        'workout_id' => $workout->id,
                        'exercise_id' => $exerciseId,
                        'set_no' => $set,
                        'reps' => $reps,
                        'weight' => $weight,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
    }
}