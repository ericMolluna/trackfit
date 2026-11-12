<?php

namespace App\Http\Controllers;

use App\Models\NutritionProfile;
use App\Models\RoutineDay;
use App\Models\Workout;
use App\Models\WorkoutSet;
use App\Support\PersonalRecords;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    /*
     * La rutina puede estar eliminada (borrado lógico):
     * en ese caso ->routine es null y devolvemos 404.
     */
    private function authorizeDay(RoutineDay $routineDay): void
    {
        abort_unless($routineDay->routine, 404);
        abort_unless($routineDay->routine->user_id === auth()->id(), 403);
    }

    public function show(RoutineDay $routineDay)
    {
        $this->authorizeDay($routineDay);

        $routineDay->load([
            'exercises' => fn ($q) => $q->orderBy('position'),
        ]);

        $workout = Workout::where('user_id', auth()->id())
            ->where('routine_day_id', $routineDay->id)
            ->latest()
            ->with('sets')
            ->first();

        $names = config('exercises.names', []);

        return view('workout-day', compact('routineDay', 'workout', 'names'));
    }

    public function start(RoutineDay $routineDay)
    {
        $this->authorizeDay($routineDay);

        Workout::create([
            'user_id' => auth()->id(),
            'routine_day_id' => $routineDay->id,
            'started_at' => now(),
            'duration_sec' => 0,
        ]);

        return redirect()->route('workout.show', $routineDay);
    }

    private function setRules(): array
    {
        return [
            'sets' => ['array'],
            'sets.*.exercise_id' => ['required', 'string', 'max:120'],
            'sets.*.set_no' => ['required', 'integer', 'min:1', 'max:20'],
            'sets.*.reps' => ['nullable', 'integer', 'min:0', 'max:200'],
            'sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
        ];
    }

    public function save(Request $request, RoutineDay $routineDay)
    {
        $this->authorizeDay($routineDay);

        $data = $request->validate([
            'workout_id' => ['required', 'integer', 'exists:workouts,id'],
            ...$this->setRules(),
        ]);

        $workout = Workout::where('id', $data['workout_id'])
            ->where('user_id', auth()->id())
            ->where('routine_day_id', $routineDay->id)
            ->firstOrFail();

        $this->saveWorkoutSets($workout, $data['sets'] ?? []);

        return back()->with('ok', 'Guardado ✅');
    }

    public function finish(Request $request, RoutineDay $routineDay)
    {
        $this->authorizeDay($routineDay);

        $data = $request->validate([
            'workout_id' => ['required', 'integer', 'exists:workouts,id'],
            'rest_seconds' => ['required', 'integer', 'in:30,60,120,180,300'],
            'intensity' => ['required', 'in:light,moderate,intense'],
            ...$this->setRules(),
        ]);

        $workout = Workout::where('id', $data['workout_id'])
            ->where('user_id', auth()->id())
            ->where('routine_day_id', $routineDay->id)
            ->firstOrFail();

        // ===== GUARDAR SERIES =====
        $this->saveWorkoutSets($workout, $data['sets'] ?? []);

        $sets = WorkoutSet::where('workout_id', $workout->id)
            ->orderBy('exercise_id')
            ->orderBy('set_no')
            ->get();

        // ===== DURACIÓN ESTIMADA =====
        // 1 minuto de trabajo por serie + descansos entre series
        // del mismo ejercicio.
        $totalSets = $sets->count();
        $activeSeconds = $totalSets * 60;

        $totalRests = 0;

        foreach ($sets->groupBy('exercise_id') as $exerciseSets) {
            $totalRests += max($exerciseSets->count() - 1, 0);
        }

        $estimatedDurationSec = $activeSeconds + $totalRests * (int) $data['rest_seconds'];

        // ===== GASTO ENERGÉTICO =====
        // kcal ≈ MET × peso corporal × horas
        $profile = NutritionProfile::where('user_id', auth()->id())->first();
        $estimatedKcal = 0;

        if ($profile && $totalSets > 0) {
            $met = match ($data['intensity']) {
                'light' => 3.0,
                'moderate' => 4.5,
                'intense' => 6.0,
                default => 4.5,
            };

            $estimatedKcal = round(
                $met * (float) $profile->weight_kg * ($estimatedDurationSec / 3600),
                2
            );
        }

        // ===== FINALIZAR ENTRENAMIENTO =====
        $workout->update([
            'ended_at' => now(),
            'duration_sec' => $estimatedDurationSec,
            'intensity' => $data['intensity'],
            'estimated_kcal' => $estimatedKcal,
        ]);

        // ===== RÉCORDS PERSONALES =====
        // Se compara con todos los entrenos anteriores del usuario.
        $records = PersonalRecords::detectNew(
            auth()->id(),
            $workout->id,
            $sets->map(function ($set) {
                $set->performed_at = now();

                return $set;
            })
        );

        return redirect()
            ->route('rutina.follow', ['routine' => $routineDay->routine_id])
            ->with(
                'ok',
                'Entreno guardado correctamente ✅ · Duración estimada: '
                . round($estimatedDurationSec / 60)
                . ' min · Gasto estimado: '
                . round($estimatedKcal)
                . ' kcal'
            )
            ->with('records', $records);
    }

    private function saveWorkoutSets(Workout $workout, array $incoming): void
    {
        $keep = [];

        foreach ($incoming as $s) {
            $keep[$s['exercise_id']][] = (int) $s['set_no'];
        }

        // Borrar series eliminadas
        foreach ($keep as $exId => $setNos) {
            WorkoutSet::where('workout_id', $workout->id)
                ->where('exercise_id', $exId)
                ->whereNotIn('set_no', $setNos)
                ->delete();
        }

        // Guardar / actualizar series
        foreach ($incoming as $s) {
            $reps = $s['reps'] ?? null;
            $weight = $s['weight'] ?? null;

            // Una serie completamente vacía no se guarda.
            if (
                ($reps === null || $reps === '')
                && ($weight === null || $weight === '')
            ) {
                WorkoutSet::where([
                    'workout_id' => $workout->id,
                    'exercise_id' => $s['exercise_id'],
                    'set_no' => $s['set_no'],
                ])->delete();

                continue;
            }

            WorkoutSet::updateOrCreate(
                [
                    'workout_id' => $workout->id,
                    'exercise_id' => $s['exercise_id'],
                    'set_no' => $s['set_no'],
                ],
                [
                    'reps' => $reps,
                    'weight' => $weight,
                ]
            );
        }
    }
}
