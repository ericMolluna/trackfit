<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RoutineFollowController extends Controller
{
    private array $dayNames = [
        'mon' => 'Lunes',
        'tue' => 'Martes',
        'wed' => 'Miércoles',
        'thu' => 'Jueves',
        'fri' => 'Viernes',
        'sat' => 'Sábado',
        'sun' => 'Domingo',
    ];

    public function show(Request $request)
    {
        $data = $request->validate([
            'routine' => ['nullable', 'integer', 'min:1'],
        ]);

        $routines = Routine::where('user_id', auth()->id())
            ->with([
                'days' => fn ($query) => $query
                    ->orderBy('position')
                    ->with(['exercises' => fn ($q) => $q->orderBy('position')]),
            ])
            ->latest()
            ->orderByDesc('id')
            ->get();

        if ($routines->isEmpty()) {
            return redirect()
                ->route('rutina.create.step1', ['nuevo' => 1])
                ->with('ok', 'Primero crea una rutina 🙂');
        }

        if (isset($data['routine'])) {
            // Solo rutinas del usuario autenticado.
            $routine = $routines->firstWhere('id', (int) $data['routine']);

            abort_unless($routine, 404);
        } else {
            $routine = $routines->first();
        }

        $names = config('exercises.names', []);
        $dayNames = $this->dayNames;
        $exerciseVisuals = $this->loadExerciseVisuals();

        // Datos para el modo edición (se usan desde JavaScript).
        $editData = [
            'name' => $routine->name,
            'days' => $routine->days->map(fn ($day) => [
                'id' => $day->id,
                'day' => $day->day,
                'exercises' => $day->exercises->pluck('exercise_id')->values(),
            ])->values(),
        ];

        $catalog = $this->buildCatalog($exerciseVisuals, $names);

        $openEditor = $request->boolean('editar');

        return view('rutina-seguir', compact(
            'routine',
            'routines',
            'names',
            'dayNames',
            'exerciseVisuals',
            'editData',
            'catalog',
            'openEditor'
        ));
    }

    /*
     * Lista de ejercicios disponibles para el buscador del editor.
     */
    private function buildCatalog(array $visuals, array $names): array
    {
        $groups = config('exercises.groups', []);
        $catalog = [];

        foreach ($visuals as $id => $visual) {
            $groupSlug = Str::before($id, '-');

            $catalog[] = [
                'id' => $id,
                'name' => $names[$id] ?? $id,
                'group' => $groups[$groupSlug] ?? ucfirst($groupSlug),
                'img' => asset($visual['frame1']),
            ];
        }

        usort(
            $catalog,
            fn ($a, $b) => [$a['group'], $a['name']] <=> [$b['group'], $b['name']]
        );

        return $catalog;
    }

    private function loadExerciseVisuals(): array
    {
        $base = public_path('img/exercices');

        if (! File::exists($base)) {
            return [];
        }

        $aliases = config('exercises.aliases', []);
        $visuals = [];

        foreach (File::directories($base) as $groupPath) {
            $groupSlug = basename($groupPath);

            foreach (File::directories($groupPath) as $exercisePath) {
                $exerciseFolder = basename($exercisePath);
                $id = Str::slug($groupSlug . '-' . $exerciseFolder);

                // Los alias usan las imágenes del ejercicio real.
                $realId = $aliases[$id] ?? $id;

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

                if ($frame1 && $frame2 && ! isset($visuals[$realId])) {
                    $visuals[$realId] = [
                        'frame1' => $frame1,
                        'frame2' => $frame2,
                    ];
                }
            }
        }

        return $visuals;
    }
}