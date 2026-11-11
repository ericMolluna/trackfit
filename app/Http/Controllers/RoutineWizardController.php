<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\RoutineExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RoutineWizardController extends Controller
{
    private array $dayOrder = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    private array $dayNames = [
        'mon' => 'Lunes',
        'tue' => 'Martes',
        'wed' => 'Miércoles',
        'thu' => 'Jueves',
        'fri' => 'Viernes',
        'sat' => 'Sábado',
        'sun' => 'Domingo',
    ];

    private function loadExercises(): array
    {
        $base = public_path('img/exercices');

        if (! File::exists($base)) {
            return [];
        }

        $names = config('exercises.names', []);
        $groupsMap = config('exercises.groups', []);
        $tags = config('exercises.tags', []);
        $aliases = config('exercises.aliases', []);

        $exercises = [];

        foreach (File::directories($base) as $groupPath) {
            $groupSlug = basename($groupPath);

            foreach (File::directories($groupPath) as $exercisePath) {
                $exerciseFolder = basename($exercisePath);

                $frame1 = null;
                $frame2 = null;

                foreach (File::files($exercisePath) as $file) {
                    $filename = $file->getFilename();

                    if (preg_match('/1\.(webp|png|jpg|jpeg)$/i', $filename)) {
                        $frame1 = "img/exercices/{$groupSlug}/{$exerciseFolder}/{$filename}";
                    }

                    if (preg_match('/2\.(webp|png|jpg|jpeg)$/i', $filename)) {
                        $frame2 = "img/exercices/{$groupSlug}/{$exerciseFolder}/{$filename}";
                    }
                }

                if (! $frame1 || ! $frame2) {
                    continue;
                }

                $id = Str::slug($groupSlug . '-' . $exerciseFolder);

                // Los alias no se muestran como ejercicios duplicados.
                if (isset($aliases[$id])) {
                    continue;
                }

                $exercises[] = [
                    'id' => $id,
                    'group' => $groupsMap[strtolower($groupSlug)] ?? ucfirst($groupSlug),
                    'name' => $names[$id] ?? $exerciseFolder,
                    'tags' => $tags[$id] ?? [strtolower($groupSlug)],
                    'frame1' => $frame1,
                    'frame2' => $frame2,
                ];
            }
        }

        usort(
            $exercises,
            fn ($a, $b) => [$a['group'], $a['name']] <=> [$b['group'], $b['name']]
        );

        return $exercises;
    }

    /*
     * Comprueba que todos los días seleccionados tienen ejercicios.
     * Devuelve el primer día vacío o null.
     */
    private function firstEmptyDay(array $wiz): ?string
    {
        foreach ($wiz['days'] as $day) {
            if (empty($wiz['by_day'][$day])) {
                return $day;
            }
        }

        return null;
    }

    public function step1(Request $request)
    {
        // ?nuevo=1 empieza una rutina desde cero
        // (descarta un borrador o una edición a medias).
        if ($request->boolean('nuevo')) {
            session()->forget('routine_wizard');
        }

        return view('rutina-crear-step1', [
            'wiz' => session('routine_wizard', []),
        ]);
    }

    public function postStep1(Request $request)
    {
        $data = $request->validate([
            'routine_name' => ['required', 'string', 'max:60'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['required', 'in:mon,tue,wed,thu,fri,sat,sun'],
        ], [
            'routine_name.required' => 'Escribe un nombre para la rutina.',
            'routine_name.max' => 'El nombre no puede superar los 60 caracteres.',
            'days.required' => 'Selecciona al menos un día de entrenamiento.',
            'days.min' => 'Selecciona al menos un día de entrenamiento.',
        ]);

        $days = array_values(array_intersect($this->dayOrder, $data['days']));

        $previous = session('routine_wizard', []);

        session([
            'routine_wizard' => [
                // Se conserva si estamos editando una rutina existente.
                'editing_id' => $previous['editing_id'] ?? null,
                'name' => $data['routine_name'],
                'days' => $days,
                'by_day' => $previous['by_day'] ?? [],
            ],
        ]);

        return redirect()->route('rutina.create.day', ['day' => $days[0]]);
    }

    public function stepDay(string $day)
    {
        $wiz = session('routine_wizard');

        if (! $wiz || ! in_array($day, $wiz['days'], true)) {
            return redirect()->route('rutina.create.step1');
        }

        $days = $wiz['days'];
        $index = array_search($day, $days, true);

        return view('rutina-crear-day', [
            'day' => $day,
            'dayName' => $this->dayNames[$day] ?? $day,
            'exercises' => $this->loadExercises(),
            'selected' => $wiz['by_day'][$day] ?? [],
            'isFirst' => $index === 0,
            'isLast' => $index === count($days) - 1,
        ]);
    }

    public function postStepDay(Request $request, string $day)
    {
        $wiz = session('routine_wizard');

        if (! $wiz || ! in_array($day, $wiz['days'], true)) {
            return redirect()->route('rutina.create.step1');
        }

        $actionData = $request->validate([
            'wizard_action' => ['sometimes', 'required', 'in:next,previous,change_days'],
        ]);

        $action = $actionData['wizard_action'] ?? 'next';

        $data = $request->validate([
            'exercises' => $action === 'next'
                ? ['required', 'array', 'min:1']
                : ['sometimes', 'array'],
            'exercises.*' => ['required', 'string', 'max:80'],
        ], [
            'exercises.required' => 'Selecciona al menos un ejercicio para continuar.',
            'exercises.min' => 'Selecciona al menos un ejercicio para continuar.',
        ]);

        $wiz['by_day'][$day] = array_values(array_unique($data['exercises'] ?? []));

        session(['routine_wizard' => $wiz]);

        if ($action === 'change_days') {
            return redirect()->route('rutina.create.step1');
        }

        $days = $wiz['days'];
        $index = array_search($day, $days, true);

        if ($action === 'previous') {
            $previousDay = $days[$index - 1] ?? null;

            return $previousDay
                ? redirect()->route('rutina.create.day', ['day' => $previousDay])
                : redirect()->route('rutina.create.step1');
        }

        $nextDay = $days[$index + 1] ?? null;

        return $nextDay
            ? redirect()->route('rutina.create.day', ['day' => $nextDay])
            : redirect()->route('rutina.create.summary');
    }

    public function prevDay(string $day)
    {
        $wiz = session('routine_wizard');

        if (! $wiz || ! in_array($day, $wiz['days'], true)) {
            return redirect()->route('rutina.create.step1');
        }

        $days = $wiz['days'];
        $index = array_search($day, $days, true);
        $previousDay = $days[$index - 1] ?? null;

        if ($previousDay === null) {
            return redirect()
                ->route('rutina.create.day', ['day' => $day])
                ->with('error', 'No hay un día anterior.');
        }

        return redirect()->route('rutina.create.day', ['day' => $previousDay]);
    }

    public function summary()
    {
        $wiz = session('routine_wizard');

        if (! $wiz || empty($wiz['days'])) {
            return redirect()->route('rutina.create.step1');
        }

        if ($emptyDay = $this->firstEmptyDay($wiz)) {
            return redirect()
                ->route('rutina.create.day', ['day' => $emptyDay])
                ->with('error', 'Selecciona los ejercicios de este día antes de finalizar.');
        }

        return view('rutina-crear-summary', [
            'wiz' => $wiz,
            'dayNames' => $this->dayNames,
        ]);
    }

    public function finish()
    {
        $wiz = session('routine_wizard');

        if (! $wiz || empty($wiz['days'])) {
            return redirect()->route('rutina.create.step1');
        }

        if ($emptyDay = $this->firstEmptyDay($wiz)) {
            return redirect()
                ->route('rutina.create.day', ['day' => $emptyDay])
                ->with('error', 'Selecciona los ejercicios de este día antes de finalizar.');
        }

        $editingId = $wiz['editing_id'] ?? null;

        if ($editingId) {
            $routine = Routine::where('id', $editingId)
                ->where('user_id', auth()->id())
                ->first();

            if (! $routine) {
                session()->forget('routine_wizard');

                return redirect()
                    ->route('rutinas.index')
                    ->with('error', 'La rutina que estabas editando ya no existe.');
            }

            DB::transaction(fn () => $this->updateRoutine($routine, $wiz));

            $message = 'Rutina actualizada ✅';
        } else {
            $routine = DB::transaction(fn () => $this->createRoutine($wiz));

            $message = 'Rutina guardada ✅';
        }

        session()->forget('routine_wizard');

        return redirect()
            ->route('rutina.follow', ['routine' => $routine->id])
            ->with('ok', $message);
    }

    private function createRoutine(array $wiz): Routine
    {
        $routine = Routine::create([
            'user_id' => auth()->id(),
            'name' => $wiz['name'],
        ]);

        foreach ($wiz['days'] as $position => $day) {
            $routineDay = RoutineDay::create([
                'routine_id' => $routine->id,
                'day' => $day,
                'position' => $position,
            ]);

            $this->saveDayExercises($routineDay, $wiz['by_day'][$day] ?? []);
        }

        return $routine;
    }

    /*
     * Actualiza la rutina SIN borrar los días que se mantienen:
     * así sus entrenos registrados siguen enlazados.
     * Los días quitados se eliminan con borrado lógico.
     */
    private function updateRoutine(Routine $routine, array $wiz): void
    {
        $routine->update(['name' => $wiz['name']]);

        $existing = $routine->days()->get()->keyBy('day');

        foreach ($wiz['days'] as $position => $day) {
            $routineDay = $existing->get($day)
                ?? RoutineDay::create([
                    'routine_id' => $routine->id,
                    'day' => $day,
                    'position' => $position,
                ]);

            $routineDay->update(['position' => $position]);

            // Los ejercicios del día sí se reemplazan:
            // workout_sets guarda exercise_id, no depende de esta tabla.
            $routineDay->exercises()->delete();

            $this->saveDayExercises($routineDay, $wiz['by_day'][$day] ?? []);
        }

        $existing
            ->except($wiz['days'])
            ->each(fn ($routineDay) => $routineDay->delete());
    }

    private function saveDayExercises(RoutineDay $routineDay, array $exerciseIds): void
    {
        foreach (array_values($exerciseIds) as $position => $exerciseId) {
            RoutineExercise::create([
                'routine_day_id' => $routineDay->id,
                'exercise_id' => $exerciseId,
                'position' => $position,
            ]);
        }
    }
}
