<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoutineController extends Controller
{
    public function create()
    {
        // Lista simple de ejercicios (luego lo sacamos de BD)
        $exercises = [
            'Press banca',
            'Press militar',
            'Dominadas',
            'Remo con barra',
            'Sentadilla',
            'Peso muerto',
            'Prensa',
            'Curl bíceps',
            'Extensión tríceps',
            'Elevaciones laterales',
            'Crunch / abdominales',
        ];

        return view('rutina-crear', compact('exercises'));
    }

    public function store(Request $request)
    {
        // De momento, solo validamos y volvemos (placeholder).
        $data = $request->validate([
            'routine_name' => ['required', 'string', 'max:60'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.name' => ['required', 'string', 'max:80'],
        ]);

        // TODO: Guardar en BD (lo hacemos luego)
        return redirect()->route('rutina.create')->with('ok', 'Rutina preparada (aún no se guarda en BD).');
    }
}
