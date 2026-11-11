<?php

namespace App\Support;

use App\Models\WorkoutSet;

/*
 * Cálculo de récords personales y 1RM estimado (fórmula de Epley).
 *
 * 1RM = peso × (1 + reps / 30)
 *
 * La estimación solo es fiable con pocas repeticiones, así que
 * solo se calcula para series de 1 a 12 repeticiones.
 */
class PersonalRecords
{
    public const MAX_REPS_FOR_1RM = 12;

    public static function estimate1rm(float $weight, int $reps): ?float
    {
        if ($weight <= 0 || $reps < 1 || $reps > self::MAX_REPS_FOR_1RM) {
            return null;
        }

        if ($reps === 1) {
            return round($weight, 1);
        }

        return round($weight * (1 + $reps / 30), 1);
    }

    /*
     * Devuelve las series válidas (peso y reps > 0) de un usuario.
     * Opcionalmente filtra por ejercicios y excluye un entreno.
     */
    public static function setsFor(
        int $userId,
        ?array $exerciseIds = null,
        ?int $excludeWorkoutId = null
    ) {
        return WorkoutSet::query()
            ->join('workouts', 'workouts.id', '=', 'workout_sets.workout_id')
            ->where('workouts.user_id', $userId)
            ->where('workout_sets.weight', '>', 0)
            ->where('workout_sets.reps', '>', 0)
            ->when(
                $exerciseIds !== null,
                fn ($query) => $query->whereIn('workout_sets.exercise_id', $exerciseIds)
            )
            ->when(
                $excludeWorkoutId !== null,
                fn ($query) => $query->where('workouts.id', '!=', $excludeWorkoutId)
            )
            ->select([
                'workout_sets.exercise_id',
                'workout_sets.weight',
                'workout_sets.reps',
                'workouts.created_at as performed_at',
            ])
            ->get();
    }

    /*
     * Agrupa series por ejercicio y calcula:
     * - best_weight: mayor peso levantado (y sus reps)
     * - best_1rm: mayor 1RM estimado (y la serie que lo produjo)
     * - sets: número de series válidas
     */
    public static function summarize(iterable $sets): array
    {
        $result = [];

        foreach ($sets as $set) {
            $exerciseId = $set->exercise_id;
            $weight = (float) $set->weight;
            $reps = (int) $set->reps;
            $date = $set->performed_at ?? null;

            if ($weight <= 0 || $reps <= 0) {
                continue;
            }

            $row = $result[$exerciseId] ?? [
                'exercise_id' => $exerciseId,
                'best_weight' => null,
                'best_1rm' => null,
                'sets' => 0,
            ];

            $row['sets']++;

            $bestWeight = $row['best_weight'];

            if (
                $bestWeight === null
                || $weight > $bestWeight['weight']
                || ($weight == $bestWeight['weight'] && $reps > $bestWeight['reps'])
            ) {
                $row['best_weight'] = [
                    'weight' => $weight,
                    'reps' => $reps,
                    'date' => $date,
                ];
            }

            $oneRm = self::estimate1rm($weight, $reps);

            if (
                $oneRm !== null
                && ($row['best_1rm'] === null || $oneRm > $row['best_1rm']['value'])
            ) {
                $row['best_1rm'] = [
                    'value' => $oneRm,
                    'weight' => $weight,
                    'reps' => $reps,
                    'date' => $date,
                ];
            }

            $result[$exerciseId] = $row;
        }

        return $result;
    }

    /*
     * Compara las series de un entreno con el historial anterior
     * y devuelve los récords batidos.
     *
     * La primera vez que se hace un ejercicio no cuenta como récord.
     */
    public static function detectNew(int $userId, int $workoutId, iterable $workoutSets): array
    {
        $current = self::summarize($workoutSets);

        if (empty($current)) {
            return [];
        }

        $previous = self::summarize(
            self::setsFor($userId, array_keys($current), $workoutId)
        );

        $names = config('exercises.names', []);
        $records = [];

        foreach ($current as $exerciseId => $now) {
            $before = $previous[$exerciseId] ?? null;

            if ($before === null) {
                continue;
            }

            $label = $names[$exerciseId] ?? $exerciseId;

            if (
                $now['best_weight']
                && $before['best_weight']
                && $now['best_weight']['weight'] > $before['best_weight']['weight']
            ) {
                $records[] = [
                    'exercise' => $label,
                    'type' => 'Peso máximo',
                    'value' => $now['best_weight']['weight'],
                    'previous' => $before['best_weight']['weight'],
                ];
            }

            if (
                $now['best_1rm']
                && (
                    $before['best_1rm'] === null
                    || $now['best_1rm']['value'] > $before['best_1rm']['value']
                )
            ) {
                $records[] = [
                    'exercise' => $label,
                    'type' => '1RM estimado',
                    'value' => $now['best_1rm']['value'],
                    'previous' => $before['best_1rm']['value'] ?? null,
                ];
            }
        }

        return $records;
    }

    public static function format(?float $value): string
    {
        if ($value === null) {
            return '—';
        }

        $text = number_format($value, 1, ',', '.');

        return str_ends_with($text, ',0') ? substr($text, 0, -2) : $text;
    }
}
