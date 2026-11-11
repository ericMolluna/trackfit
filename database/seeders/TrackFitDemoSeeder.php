<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TrackFitDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new RuntimeException(
                'Este seeder solo se puede ejecutar en entorno local.'
            );
        }

        $userId = 10;
        $routineName = 'DEMO TrackFit - 3 días';

        $user = DB::table('users')
            ->where('id', $userId)
            ->first();

        if (! $user || $user->name !== 'EricMG') {
            throw new RuntimeException(
                'El usuario 10 no existe o no se llama EricMG. No se ha importado nada.'
            );
        }

        $days = [
            [
                'day' => 'mon',
                'offset' => 0,
                'exercises' => [
                    [
                        'id' => 'pecho-pbancab',
                        'weight' => 40,
                        'increment' => 2.5,
                    ],
                    [
                        'id' => 'espalda-jalonc',
                        'weight' => 35,
                        'increment' => 2.5,
                    ],
                    [
                        'id' => 'hombro-laterales',
                        'weight' => 6,
                        'increment' => 0.5,
                    ],
                    [
                        'id' => 'triceps-tricepsp',
                        'weight' => 20,
                        'increment' => 2.5,
                    ],
                ],
            ],
            [
                'day' => 'wed',
                'offset' => 2,
                'exercises' => [
                    [
                        'id' => 'cuadriceps-sentadilla',
                        'weight' => 50,
                        'increment' => 2.5,
                    ],
                    [
                        'id' => 'cuadriceps-prensa',
                        'weight' => 100,
                        'increment' => 5,
                    ],
                    [
                        'id' => 'femoral-femorals',
                        'weight' => 30,
                        'increment' => 2.5,
                    ],
                    [
                        'id' => 'gemelo-gemelom',
                        'weight' => 40,
                        'increment' => 2.5,
                    ],
                ],
            ],
            [
                'day' => 'fri',
                'offset' => 4,
                'exercises' => [
                    [
                        'id' => 'pecho-pbancaincm',
                        'weight' => 16,
                        'increment' => 1,
                    ],
                    [
                        'id' => 'espalda-remouni',
                        'weight' => 25,
                        'increment' => 2.5,
                    ],
                    [
                        'id' => 'hombro-militar',
                        'weight' => 25,
                        'increment' => 2.5,
                    ],
                    [
                        'id' => 'biceps-predicador',
                        'weight' => 15,
                        'increment' => 1,
                    ],
                ],
            ],
        ];

        /*
         * Cada fila representa un alimento registrado.
         * Los gramos son datos ficticios para la demo.
         */
        $menu = [
            ['meal' => 'breakfast', 'food' => 'Avena', 'grams' => 60],
            ['meal' => 'breakfast', 'food' => 'Leche semidesnatada', 'grams' => 250],
            ['meal' => 'breakfast', 'food' => 'Plátano', 'grams' => 120],

            ['meal' => 'lunch', 'food' => 'Arroz blanco cocido', 'grams' => 200],
            ['meal' => 'lunch', 'food' => 'Pechuga de pollo', 'grams' => 150],
            ['meal' => 'lunch', 'food' => 'Brócoli', 'grams' => 150],

            ['meal' => 'snack', 'food' => 'Yogur natural', 'grams' => 125],
            ['meal' => 'snack', 'food' => 'Manzana', 'grams' => 180],

            ['meal' => 'dinner', 'food' => 'Salmón', 'grams' => 150],
            ['meal' => 'dinner', 'food' => 'Quinoa cocida', 'grams' => 180],
            ['meal' => 'dinner', 'food' => 'Calabacín', 'grams' => 150],
        ];

        $foodNames = array_values(
            array_unique(array_column($menu, 'food'))
        );

        $foodIds = DB::table('foods')
            ->whereIn('name', $foodNames)
            ->pluck('id', 'name');

        foreach ($foodNames as $name) {
            if (! $foodIds->has($name)) {
                throw new RuntimeException(
                    "Falta el alimento '{$name}'. No se ha importado nada."
                );
            }
        }

        $exerciseNames = config('exercises.names', []);

        foreach ($days as $day) {
            foreach ($day['exercises'] as $exercise) {
                if (! array_key_exists($exercise['id'], $exerciseNames)) {
                    throw new RuntimeException(
                        "Falta el ejercicio '{$exercise['id']}' en config/exercises.php."
                    );
                }
            }
        }

        $today = CarbonImmutable::today();
        $now = CarbonImmutable::now();

        $profile = DB::table('nutrition_profiles')
            ->where('user_id', $userId)
            ->first();

        $weightKg = $profile
            ? (float) $profile->weight_kg
            : 0;

        $counts = DB::transaction(function () use (
            $userId,
            $routineName,
            $days,
            $menu,
            $foodIds,
            $today,
            $now,
            $weightKg
        ) {
            /*
             * Esta rutina actúa como marca de la importación.
             * Si sigue existiendo, no se vuelve a cargar la demo.
             */
            $alreadyImported = DB::table('routines')
                ->where('user_id', $userId)
                ->where('name', $routineName)
                ->exists();

            if ($alreadyImported) {
                throw new RuntimeException(
                    'La rutina de demo ya existe. Se cancela la importación para evitar duplicados.'
                );
            }

            $routineId = DB::table('routines')->insertGetId([
                'user_id' => $userId,
                'name' => $routineName,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ]);

            $routineDayIds = [];

            foreach ($days as $position => $day) {
                $routineDayId = DB::table('routine_days')->insertGetId([
                    'routine_id' => $routineId,
                    'day' => $day['day'],
                    'position' => $position,
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ]);

                $routineDayIds[$day['day']] = $routineDayId;

                foreach ($day['exercises'] as $exercisePosition => $exercise) {
                    DB::table('routine_exercises')->insert([
                        'routine_day_id' => $routineDayId,
                        'exercise_id' => $exercise['id'],
                        'position' => $exercisePosition,
                        'created_at' => $now->toDateTimeString(),
                        'updated_at' => $now->toDateTimeString(),
                    ]);
                }
            }

            /*
             * Cuatro semanas completas anteriores a la actual.
             * No se generan entrenamientos con fechas futuras.
             */
            $firstMonday = $today
                ->startOfWeek(CarbonImmutable::MONDAY)
                ->subWeeks(4);

            $workoutCount = 0;
            $setCount = 0;

            foreach (range(0, 3) as $week) {
                foreach ($days as $day) {
                    $startedAt = $firstMonday
                        ->addWeeks($week)
                        ->addDays($day['offset'])
                        ->setTime(18, 0);

                    $exerciseCount = count($day['exercises']);
                    $setsPerExercise = 3;
                    $restSeconds = 90;

                    $totalSets = $exerciseCount * $setsPerExercise;

                    $totalRests = $exerciseCount
                        * ($setsPerExercise - 1);

                    $durationSec = ($totalSets * 60)
                        + ($totalRests * $restSeconds);

                    $endedAt = $startedAt->addSeconds($durationSec);

                    /*
                     * Reutiliza la fórmula de tu controlador.
                     * Si no hay perfil con peso, se guarda 0.
                     */
                    $estimatedKcal = $weightKg > 0
                        ? round(4.5 * $weightKg * ($durationSec / 3600), 2)
                        : 0;

                    $workoutId = DB::table('workouts')->insertGetId([
                        'user_id' => $userId,
                        'routine_day_id' => $routineDayIds[$day['day']],
                        'started_at' => $startedAt->toDateTimeString(),
                        'ended_at' => $endedAt->toDateTimeString(),
                        'duration_sec' => $durationSec,
                        'intensity' => 'moderate',
                        'estimated_kcal' => $estimatedKcal,
                        'created_at' => $startedAt->toDateTimeString(),
                        'updated_at' => $endedAt->toDateTimeString(),
                    ]);

                    $workoutCount++;

                    foreach ($day['exercises'] as $exercise) {
                        $weight = $exercise['weight']
                            + ($week * $exercise['increment']);

                        foreach (range(1, $setsPerExercise) as $setNo) {
                            DB::table('workout_sets')->insert([
                                'workout_id' => $workoutId,
                                'exercise_id' => $exercise['id'],
                                'set_no' => $setNo,
                                'reps' => 10,
                                'weight' => $weight,
                                'created_at' => $startedAt->toDateTimeString(),
                                'updated_at' => $endedAt->toDateTimeString(),
                            ]);

                            $setCount++;
                        }
                    }
                }
            }

            /*
             * Alimentación de los últimos 14 días, incluido hoy.
             * Variación pequeña y determinista de cantidades.
             */
            $foodLogCount = 0;

            foreach (range(0, 13) as $dayIndex) {
                $date = $today->subDays(13 - $dayIndex);

                $factor = [0.95, 1.00, 1.05][$dayIndex % 3];

                foreach ($menu as $entry) {
                    $grams = (int) round(
                        ($entry['grams'] * $factor) / 5
                    ) * 5;

                    DB::table('food_logs')->insert([
                        'user_id' => $userId,
                        'date' => $date->toDateString(),
                        'meal' => $entry['meal'],
                        'food_id' => $foodIds->get($entry['food']),
                        'grams' => $grams,
                        'created_at' => $date->toDateTimeString(),
                        'updated_at' => $date->toDateTimeString(),
                    ]);

                    $foodLogCount++;
                }
            }

            return [
                'routine_id' => $routineId,
                'workouts' => $workoutCount,
                'sets' => $setCount,
                'food_logs' => $foodLogCount,
            ];
        });

        $this->command?->info('Demo de TrackFit importada correctamente.');
        $this->command?->line("Usuario: {$userId} — EricMG");
        $this->command?->line("Rutina creada: {$counts['routine_id']}");
        $this->command?->line("Entrenamientos: {$counts['workouts']}");
        $this->command?->line("Series: {$counts['sets']}");
        $this->command?->line("Registros de alimentos: {$counts['food_logs']}");

        if ($weightKg <= 0) {
            $this->command?->warn(
                'No se encontró un peso válido: los entrenamientos de demo tienen estimated_kcal = 0.'
            );
        }
    }
}