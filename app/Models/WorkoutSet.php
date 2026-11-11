<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSet extends Model
{
    protected $fillable = [
        'workout_id',
        'exercise_id',
        'set_no',
        'reps',
        'weight',
        'created_at',
        'updated_at',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
}