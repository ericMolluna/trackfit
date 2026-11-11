<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\RoutineExercise;
use Illuminate\Support\Facades\DB;

class PredefinedRoutineController extends Controller
{
    /**
     * Lista de rutinas predefinidas.
     */
    public function index()
    {
        $routines = config('predefined_routines', []);

        return view(
            'routines.predefined.index',
            compact('routines')
        );
    }


    /**
     * Detalle de una rutina predefinida.
     */
    public function show(string $routineKey)
    {
        $routine = config(
            "predefined_routines.$routineKey"
        );

        if (!$routine) {
            abort(404);
        }

        $exerciseNames = config(
            'exercises.names',
            []
        );

        return view(
            'routines.predefined.show',
            [
                'routine' => $routine,
                'routineKey' => $routineKey,
                'exerciseNames' => $exerciseNames,
            ]
        );
    }


    /**
     * Convierte una rutina predefinida
     * en una rutina normal del usuario.
     */
    public function use(string $routineKey)
    {
        $template = config(
            "predefined_routines.$routineKey"
        );

        if (!$template) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Días de entrenamiento
        |--------------------------------------------------------------------------
        |
        | RoutineDay necesita mon, tue, wed...
        | Como nuestras plantillas tienen 4 o 5 días,
        | asignamos una distribución semanal razonable.
        |
        */

        $daySchedules = [

            'upper-lower-4' => [
                'mon',
                'tue',
                'thu',
                'fri',
            ],

            'ppl-5' => [
                'mon',
                'tue',
                'wed',
                'fri',
                'sat',
            ],

            'fullbody-4' => [
                'mon',
                'tue',
                'thu',
                'sat',
            ],

        ];


        $schedule = $daySchedules[$routineKey] ?? [
            'mon',
            'tue',
            'wed',
            'thu',
            'fri',
            'sat',
            'sun',
        ];


        /*
        |--------------------------------------------------------------------------
        | IDs válidos de ejercicios
        |--------------------------------------------------------------------------
        |
        | Así evitamos guardar accidentalmente un ejercicio
        | que no exista en config/exercises.php.
        |
        */

        $validExercises = config(
            'exercises.names',
            []
        );


        /*
        |--------------------------------------------------------------------------
        | Crear rutina
        |--------------------------------------------------------------------------
        */

        $routine = DB::transaction(
            function () use (
                $template,
                $schedule,
                $validExercises
            ) {

                $routine = Routine::create([
                    'user_id' => auth()->id(),
                    'name' => $template['title'],
                ]);


                foreach (
                    $template['days']
                    as $position => $templateDay
                ) {

                    $dayCode =
                        $schedule[$position]
                        ?? null;


                    if (!$dayCode) {
                        continue;
                    }


                    $routineDay = RoutineDay::create([
                        'routine_id' => $routine->id,
                        'day' => $dayCode,
                        'position' => $position,
                    ]);


                    foreach (
                        $templateDay['exercises']
                        as $exercisePosition => $exerciseId
                    ) {

                        /*
                         * Si no existe en exercises.names,
                         * no lo guardamos.
                         */
                        if (
                            !array_key_exists(
                                $exerciseId,
                                $validExercises
                            )
                        ) {
                            continue;
                        }


                        RoutineExercise::create([
                            'routine_day_id' => $routineDay->id,
                            'exercise_id' => $exerciseId,
                            'position' => $exercisePosition,
                        ]);
                    }
                }


                return $routine;
            }
        );


        return redirect()
            ->route('rutina.follow')
            ->with(
                'ok',
                'Rutina "' .
                $routine->name .
                '" añadida correctamente ✅'
            );
    }
}