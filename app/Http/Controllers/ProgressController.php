<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Models\Workout;
use App\Models\WorkoutSet;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $exerciseNames = config('exercises.names', []);

        $exerciseIds = collect(array_keys($exerciseNames))
            ->sort()
            ->values();

        $mode = $request->query('mode', 'general');

        if ($mode === 'specific') {
            $mode = 'exercise';
        }

        if (! in_array($mode, ['general', 'exercise'], true)) {
            $mode = 'general';
        }

        $groupBy = $request->query('group_by', 'week');

        if (! in_array($groupBy, ['week', 'month', 'quarter', 'year'], true)) {
            $groupBy = 'week';
        }

        $selectedExercise = $request->query('exercise');

        if (
            ! is_string($selectedExercise)
            || ! in_array($selectedExercise, $exerciseIds->all(), true)
        ) {
            $selectedExercise = null;
        }

        if ($mode === 'general') {
            $selectedExercise = null;
        }

        $periodRows = [];
        $chartLabels = [];
        $chartData = [];

        $bestPeriodLabel = null;
        $bestPeriodVolume = null;
        $lastPeriodLabel = null;
        $lastPeriodVolume = null;

        $hasAnyData = Workout::where('user_id', $userId)->exists();

        if ($mode === 'general' || $selectedExercise !== null) {
            $periodSql = $this->periodSql($groupBy);

            $query = WorkoutSet::query()
                ->join(
                    'workouts',
                    'workouts.id',
                    '=',
                    'workout_sets.workout_id'
                )
                ->where('workouts.user_id', $userId);

            if ($mode === 'exercise') {
                $query->where(
                    'workout_sets.exercise_id',
                    $selectedExercise
                );
            }

            $rows = $query
                ->selectRaw("
                    {$periodSql} as period_key,
                    SUM(
                        COALESCE(workout_sets.weight, 0)
                        * COALESCE(workout_sets.reps, 0)
                    ) as volume,
                    MAX(COALESCE(workout_sets.weight, 0)) as best_weight,
                    MAX(COALESCE(workout_sets.reps, 0)) as best_reps,
                    COUNT(workout_sets.id) as sets,
                    COUNT(DISTINCT workouts.id) as workouts
                ")
                ->groupByRaw($periodSql)
                ->orderByRaw("{$periodSql} ASC")
                ->get();

            $previousVolume = null;

            foreach ($rows as $row) {
                $volume = (float) $row->volume;

                $difference = $previousVolume === null
                    ? null
                    : $volume - $previousVolume;

                $label = $this->formatPeriod(
                    (string) $row->period_key,
                    $groupBy
                );

                $periodRows[] = [
                    'label' => $label,
                    'volume' => $volume,
                    'best_weight' => (float) $row->best_weight,
                    'best_reps' => (int) $row->best_reps,
                    'sets' => (int) $row->sets,
                    'workouts' => (int) $row->workouts,
                    'diff' => $difference,
                ];

                $chartLabels[] = $label;
                $chartData[] = round($volume, 1);

                $previousVolume = $volume;
            }

            if (! empty($periodRows)) {
                $best = collect($periodRows)
                    ->sortByDesc('volume')
                    ->first();

                $last = collect($periodRows)->last();

                $bestPeriodLabel = $best['label'];
                $bestPeriodVolume = $best['volume'];

                $lastPeriodLabel = $last['label'];
                $lastPeriodVolume = $last['volume'];
            }
        }

        return view('progress-index', compact(
            'exerciseIds',
            'exerciseNames',
            'selectedExercise',
            'mode',
            'groupBy',
            'periodRows',
            'chartLabels',
            'chartData',
            'bestPeriodLabel',
            'bestPeriodVolume',
            'lastPeriodLabel',
            'lastPeriodVolume',
            'hasAnyData'
        ));
    }

    /**
     * Clave de agrupación y ordenación.
     *
     * Se mantiene created_at como fecha del entrenamiento,
     * igual que en el controlador anterior.
     */
    private function periodSql(string $groupBy): string
    {
        // En el servidor usamos PostgreSQL y en local SQLite:
        // cada uno tiene sus propias funciones de fecha.
        if (DB::connection()->getDriverName() === 'pgsql') {
            return match ($groupBy) {
                'month' => "to_char(workouts.created_at, 'YYYY-MM')",
                'quarter' => "to_char(workouts.created_at, 'YYYY') || '-T' || CAST(EXTRACT(QUARTER FROM workouts.created_at) AS INTEGER)",
                'year' => "to_char(workouts.created_at, 'YYYY')",
                // Lunes de la semana (igual que la versión SQLite)
                default => "to_char(date_trunc('week', workouts.created_at), 'YYYY-MM-DD')",
            };
        }

        return match ($groupBy) {
            'month' =>
                "strftime('%Y-%m', workouts.created_at)",

            'quarter' =>
                "strftime('%Y', workouts.created_at)
                    || '-T'
                    || CAST(
                        (
                            (
                                CAST(
                                    strftime('%m', workouts.created_at)
                                    AS INTEGER
                                ) - 1
                            ) / 3
                        ) + 1
                        AS INTEGER
                    )",

            'year' =>
                "strftime('%Y', workouts.created_at)",

            default =>
                "date(workouts.created_at, 'weekday 0', '-6 days')",
        };
    }

    /**
     * Etiquetas legibles en español.
     */
    private function formatPeriod(string $key, string $groupBy): string
    {
        if ($groupBy === 'year') {
            return 'Año ' . $key;
        }

        if ($groupBy === 'month') {
            $date = CarbonImmutable::parse($key . '-01')
                ->locale('es');

            return mb_convert_case(
                $date->translatedFormat('F Y'),
                MB_CASE_TITLE,
                'UTF-8'
            );
        }

        if ($groupBy === 'quarter') {
            [$year, $quarter] = explode('-T', $key);

            $ranges = [
                1 => 'Enero–marzo',
                2 => 'Abril–junio',
                3 => 'Julio–septiembre',
                4 => 'Octubre–diciembre',
            ];

            return $ranges[(int) $quarter] . ' ' . $year;
        }

        $start = CarbonImmutable::parse($key)->locale('es');
        $end = $start->addDays(6);

        // Semana que atraviesa un cambio de año.
        if ($start->year !== $end->year) {
            return $start->translatedFormat('j M Y')
                . ' – '
                . $end->translatedFormat('j M Y');
        }

        // Semana que atraviesa un cambio de mes.
        if ($start->month !== $end->month) {
            return $start->translatedFormat('j M')
                . ' – '
                . $end->translatedFormat('j M Y');
        }

        return $start->format('j')
            . '–'
            . $end->translatedFormat('j M Y');
    }
}