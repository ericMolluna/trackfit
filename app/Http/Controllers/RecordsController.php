<?php

namespace App\Http\Controllers;

use App\Support\PersonalRecords;

class RecordsController extends Controller
{
    public function index()
    {
        $names = config('exercises.names', []);

        $records = collect(
            PersonalRecords::summarize(
                PersonalRecords::setsFor(auth()->id())
            )
        )
            ->map(function ($row) use ($names) {
                $row['name'] = $names[$row['exercise_id']] ?? $row['exercise_id'];

                return $row;
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view('progress-records', [
            'records' => $records,
            'maxReps' => PersonalRecords::MAX_REPS_FOR_1RM,
        ]);
    }
}
