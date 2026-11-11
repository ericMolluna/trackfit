<?php

namespace App\Http\Controllers;

use App\Models\RoutineDay;
use App\Models\Workout;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private array $dayKeys = [
        1 => 'mon',
        2 => 'tue',
        3 => 'wed',
        4 => 'thu',
        5 => 'fri',
        6 => 'sat',
        7 => 'sun',
    ];

    private array $dayNames = [
        'mon' => 'Lunes',
        'tue' => 'Martes',
        'wed' => 'Miércoles',
        'thu' => 'Jueves',
        'fri' => 'Viernes',
        'sat' => 'Sábado',
        'sun' => 'Domingo',
    ];

    public function index()
    {
        $userId = auth()->id();
        $todayIso = now()->dayOfWeekIso;
        $todayKey = $this->dayKeys[$todayIso];

        // Todos los días de todas las rutinas del usuario.
        // whereHas ignora automáticamente las rutinas eliminadas.
        $routineDays = RoutineDay::whereHas(
            'routine',
            fn ($query) => $query->where('user_id', $userId)
        )
            ->with([
                'routine',
                'exercises' => fn ($query) => $query->orderBy('position'),
            ])
            ->get()
            ->sortBy(fn ($day) => [
                $day->routine->name,
                array_search($day->day, $this->dayKeys, true),
            ])
            ->values();

        $todayDays = $routineDays
            ->where('day', $todayKey)
            ->values();

        // Días de rutina que ya se han terminado hoy.
        $doneToday = Workout::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->whereNotNull('ended_at')
            ->pluck('routine_day_id')
            ->all();

        // Si hoy no toca nada, buscamos el próximo día con entreno.
        $nextDayKey = null;
        $nextDays = collect();

        if ($todayDays->isEmpty() && $routineDays->isNotEmpty()) {
            for ($offset = 1; $offset <= 6; $offset++) {
                $key = $this->dayKeys[(($todayIso - 1 + $offset) % 7) + 1];
                $candidates = $routineDays->where('day', $key)->values();

                if ($candidates->isNotEmpty()) {
                    $nextDayKey = $key;
                    $nextDays = $candidates;
                    break;
                }
            }
        }

        return view('dashboard', [
            'todayKey' => $todayKey,
            'todayDays' => $todayDays,
            'routineDays' => $routineDays,
            'doneToday' => $doneToday,
            'nextDayKey' => $nextDayKey,
            'nextDays' => $nextDays,
            'dayNames' => $this->dayNames,
            'exerciseNames' => config('exercises.names', []),
        ]);
    }

    /*
     * Empieza el entreno de cualquier día de cualquier rutina
     * del usuario, aunque no sea el previsto para hoy.
     */
    public function start(Request $request)
    {
        $data = $request->validate([
            'routine_day_id' => ['required', 'integer'],
        ], [
            'routine_day_id.required' => 'Elige qué quieres entrenar.',
        ]);

        $routineDay = RoutineDay::whereKey($data['routine_day_id'])
            ->whereHas(
                'routine',
                fn ($query) => $query->where('user_id', auth()->id())
            )
            ->firstOrFail();

        Workout::create([
            'user_id' => auth()->id(),
            'routine_day_id' => $routineDay->id,
            'started_at' => now(),
            'duration_sec' => 0,
        ]);

        return redirect()->route('workout.show', $routineDay);
    }
}
