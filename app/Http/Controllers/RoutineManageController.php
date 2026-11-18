<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\RoutineExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoutineManageController extends Controller
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

    private function authorizeRoutine(Routine $routine): void
    {
        abort_unless($routine->user_id === auth()->id(), 403);
    }

    public function index()
    {
        $userId = auth()->id();

        $routines = Routine::where('user_id', $userId)
            ->with([
                'days' => fn ($query) => $query
                    ->orderBy('position')
                    ->withCount('exercises'),
            ])
            ->latest()
            ->orderByDesc('id')
            ->get();

        // Entrenos registrados por rutina (para avisar al eliminar).
        $workoutCounts = DB::table('workouts')
            ->join('routine_days', 'routine_days.id', '=', 'workouts.routine_day_id')
            ->where('workouts.user_id', $userId)
            ->groupBy('routine_days.routine_id')
            ->selectRaw('routine_days.routine_id, COUNT(*) as total')
            ->pluck('total', 'routine_days.routine_id');

        return view('rutinas-gestionar', [
            'routines' => $routines,
            'workoutCounts' => $workoutCounts,
            'dayNames' => $this->dayNames,
        ]);
    }

    /*
     * Editar ya no usa el asistente de creación:
     * abre la propia rutina con el editor activado.
     */
    public function edit(Routine $routine)
    {
        $this->authorizeRoutine($routine);

        return redirect()->route('rutina.follow', [
            'routine' => $routine,
            'editar' => 1,
        ]);
    }

    /*
     * Guarda los cambios hechos en el editor de la página de la rutina.
     *
     * Los días que se mantienen conservan su id, así que sus entrenos
     * registrados siguen enlazados aunque cambies el día de la semana.
     * Los días quitados se eliminan con borrado lógico.
     */
    public function update(Request $request, Routine $routine)
    {
        $this->authorizeRoutine($routine);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'days' => ['required', 'array', 'min:1', 'max:7'],
            'days.*.id' => ['nullable', 'integer'],
            'days.*.day' => ['required', 'distinct', 'in:mon,tue,wed,thu,fri,sat,sun'],
            'days.*.exercises' => ['required', 'array', 'min:1'],
            'days.*.exercises.*' => ['required', 'string', 'max:80'],
        ], [
            'name.required' => 'Escribe un nombre para la rutina.',
            'name.max' => 'El nombre no puede superar los 60 caracteres.',
            'days.required' => 'La rutina necesita al menos un día.',
            'days.*.day.distinct' => 'Hay dos días de la semana repetidos.',
            'days.*.exercises.required' => 'Cada día necesita al menos un ejercicio.',
            'days.*.exercises.min' => 'Cada día necesita al menos un ejercicio.',
        ]);

        // Ordenamos los días según la semana.
        $days = collect($data['days'])
            ->sortBy(fn ($day) => array_search($day['day'], $this->dayOrder, true))
            ->values();

        DB::transaction(function () use ($routine, $data, $days) {
            $routine->update(['name' => $data['name']]);

            $existing = $routine->days()->get()->keyBy('id');
            $keptIds = [];

            foreach ($days as $position => $dayData) {
                $id = isset($dayData['id']) ? (int) $dayData['id'] : null;

                // Solo se reutilizan días que pertenecen a esta rutina.
                $routineDay = $id ? $existing->get($id) : null;

                if ($routineDay) {
                    $routineDay->update([
                        'day' => $dayData['day'],
                        'position' => $position,
                    ]);
                } else {
                    $routineDay = RoutineDay::create([
                        'routine_id' => $routine->id,
                        'day' => $dayData['day'],
                        'position' => $position,
                    ]);
                }

                $keptIds[] = $routineDay->id;

                // workout_sets guarda exercise_id, no depende de esta tabla.
                $routineDay->exercises()->delete();

                $exerciseIds = array_values(array_unique($dayData['exercises']));

                foreach ($exerciseIds as $index => $exerciseId) {
                    RoutineExercise::create([
                        'routine_day_id' => $routineDay->id,
                        'exercise_id' => $exerciseId,
                        'position' => $index,
                    ]);
                }
            }

            $existing
                ->except($keptIds)
                ->each(fn ($routineDay) => $routineDay->delete());
        });

        return redirect()
            ->route('rutina.follow', ['routine' => $routine])
            ->with('ok', 'Cambios guardados ✅');
    }

    public function duplicate(Request $request, Routine $routine)
    {
        $this->authorizeRoutine($routine);

        $routine->load([
            'days' => fn ($query) => $query
                ->orderBy('position')
                ->with(['exercises' => fn ($q) => $q->orderBy('position')]),
        ]);

        $copy = DB::transaction(function () use ($routine) {
            $copy = Routine::create([
                'user_id' => auth()->id(),
                'name' => Str::limit($routine->name, 50, '') . ' (copia)',
            ]);

            foreach ($routine->days as $routineDay) {
                $newDay = RoutineDay::create([
                    'routine_id' => $copy->id,
                    'day' => $routineDay->day,
                    'position' => $routineDay->position,
                ]);

                foreach ($routineDay->exercises as $exercise) {
                    RoutineExercise::create([
                        'routine_day_id' => $newDay->id,
                        'exercise_id' => $exercise->exercise_id,
                        'position' => $exercise->position,
                    ]);
                }
            }

            return $copy;
        });

        $message = "Rutina duplicada como «{$copy->name}».";

        if ($request->input('from') === 'manage') {
            return redirect()->route('rutinas.index')->with('ok', $message);
        }

        // Desde la página de la rutina abrimos directamente la copia.
        return redirect()
            ->route('rutina.follow', ['routine' => $copy])
            ->with('ok', $message);
    }

    /*
     * Borrado lógico: la rutina desaparece de la app, pero
     * los entrenos ya registrados se conservan para el progreso
     * y los récords.
     */
    public function destroy(Request $request, Routine $routine)
    {
        $this->authorizeRoutine($routine);

        $name = $routine->name;

        $routine->delete();

        // Si se estaba editando esta rutina, descartamos el borrador.
        if ((session('routine_wizard.editing_id')) === $routine->id) {
            session()->forget('routine_wizard');
        }

        // Desde la página de la rutina volvemos a «Mis rutinas»;
        // desde «Gestionar rutinas», a esa misma lista.
        $route = $request->input('from') === 'manage'
            ? 'rutinas.index'
            : 'rutina.follow';

        return redirect()
            ->route($route)
            ->with('ok', "Rutina «{$name}» eliminada. Tu historial de entrenos se conserva.");
    }
}