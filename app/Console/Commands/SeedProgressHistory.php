<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\Workout;
use App\Models\WorkoutSet;
use Carbon\Carbon;

class SeedProgressHistory extends Command
{
    protected $signature = 'trackfit:seed-progress {email}';
    protected $description = 'Genera historial falso de progreso para un usuario por email';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró ningún usuario con email: {$email}");
            return self::FAILURE;
        }

        $exerciseNames = config('exercises.names', []);
        $exerciseIds = array_keys($exerciseNames);

        if (empty($exerciseIds)) {
            $this->error('No hay ejercicios en config/exercises.php');
            return self::FAILURE;
        }

        // Limpiar historial previo del usuario
        $workoutIds = Workout::where('user_id', $user->id)->pluck('id');
        if ($workoutIds->isNotEmpty()) {
            WorkoutSet::whereIn('workout_id', $workoutIds)->delete();
            Workout::whereIn('id', $workoutIds)->delete();
        }

        $routineIds = Routine::where('user_id', $user->id)->pluck('id');
        if ($routineIds->isNotEmpty()) {
            RoutineDay::whereIn('routine_id', $routineIds)->delete();
            Routine::whereIn('id', $routineIds)->delete();
        }

        // Rutina demo base
        $routine = Routine::create([
            'user_id' => $user->id,
            'name' => 'Historial demo progreso',
        ]);

        $routineDay = RoutineDay::create([
            'routine_id' => $routine->id,
            'day' => 'mon',
            'position' => 1,
        ]);

        // 6 semanas de historial por ejercicio
        $start = Carbon::now()->subWeeks(6)->startOfWeek();

        foreach ($exerciseIds as $exerciseId) {
            $baseWeight = $this->baseWeightForExercise($exerciseId);

            for ($w = 0; $w < 6; $w++) {
                $date = $start->copy()
                    ->addWeeks($w)
                    ->addDays(rand(0, 5))
                    ->setTime(18, 0);

                $workout = Workout::create([
                    'user_id' => $user->id,
                    'routine_day_id' => $routineDay->id,
                    'started_at' => $date,
                    'ended_at' => $date->copy()->addHour(),
                    'duration_sec' => 3600,
                    'created_at' => $date,
                    'updated_at' => $date->copy()->addHour(),
                ]);

                for ($set = 1; $set <= 3; $set++) {
                    [$weight, $reps] = $this->progressionForExercise(
                        $exerciseId,
                        $baseWeight,
                        $w,
                        $set
                    );

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

        $this->info("Historial de progreso generado para {$email}");
        return self::SUCCESS;
    }

    private function baseWeightForExercise(string $exerciseId): float
    {
        return match (true) {
            str_contains($exerciseId, 'pbanca')      => 80,
            str_contains($exerciseId, 'sentadilla')  => 100,
            str_contains($exerciseId, 'militar')     => 45,
            str_contains($exerciseId, 'dominadas')   => 0,
            str_contains($exerciseId, 'remo')        => 60,
            str_contains($exerciseId, 'jalon')       => 55,
            str_contains($exerciseId, 'ext')         => 50,
            str_contains($exerciseId, 'prensa')      => 140,
            str_contains($exerciseId, 'fondos')      => 0,
            str_contains($exerciseId, 'laterales')   => 12,
            str_contains($exerciseId, 'aperturas')   => 45,
            str_contains($exerciseId, 'predicador')  => 25,
            str_contains($exerciseId, 'biceps')      => 20,
            str_contains($exerciseId, 'triceps')     => 25,
            str_contains($exerciseId, 'rompec')      => 30,
            str_contains($exerciseId, 'femoral')     => 40,
            str_contains($exerciseId, 'gemelo')      => 70,
            str_contains($exerciseId, 'aductor')     => 45,
            str_contains($exerciseId, 'muneca')      => 15,
            default                                  => 40,
        };
    }

    private function progressionForExercise(string $exerciseId, float $baseWeight, int $week, int $set): array
    {
        return match (true) {
            // Press banca: subida clara de peso
            str_contains($exerciseId, 'pbanca') => [
                $baseWeight + (floor($week / 2) * 2.5),
                max(4, 8 - ($set - 1) + ($week % 2)),
            ],

            // Sentadilla: sube más fuerte
            str_contains($exerciseId, 'sentadilla') => [
                $baseWeight + ($week * 3),
                max(4, 7 - ($set - 1) + ($week % 2)),
            ],

            // Militar: subida lenta
            str_contains($exerciseId, 'militar') => [
                $baseWeight + (floor($week / 3) * 1.25),
                max(4, 9 - ($set - 1)),
            ],

            // Dominadas: progresa sobre todo en reps
            str_contains($exerciseId, 'dominadas') => [
                0,
                max(4, 6 + $week - ($set - 1)),
            ],

            // Remo / jalón: progreso medio
            str_contains($exerciseId, 'remo') || str_contains($exerciseId, 'jalon') => [
                $baseWeight + (floor($week / 2) * 2),
                max(4, 10 - ($set - 1)),
            ],

            // Laterales / extensiones / aislamiento: poco peso, más reps
            str_contains($exerciseId, 'laterales')
                || str_contains($exerciseId, 'ext')
                || str_contains($exerciseId, 'triceps')
                || str_contains($exerciseId, 'predicador')
                || str_contains($exerciseId, 'biceps') => [
                $baseWeight + (floor($week / 4) * 1),
                max(6, 12 - ($set - 1) + ($week % 3)),
            ],

            // Fondos: reps suben más que el peso
            str_contains($exerciseId, 'fondos') => [
                0,
                max(5, 8 + $week - ($set - 1)),
            ],

            // Prensa: subida notable
            str_contains($exerciseId, 'prensa') => [
                $baseWeight + ($week * 4),
                max(6, 10 - ($set - 1)),
            ],

            // Gemelo / femoral / aductor: progreso suave
            str_contains($exerciseId, 'gemelo')
                || str_contains($exerciseId, 'femoral')
                || str_contains($exerciseId, 'aductor') => [
                $baseWeight + (floor($week / 2) * 2),
                max(6, 11 - ($set - 1)),
            ],

            // Caso por defecto
            default => [
                $baseWeight + (floor($week / 2) * 1.5),
                max(5, 9 - ($set - 1) + ($week % 2)),
            ],
        };
    }
}