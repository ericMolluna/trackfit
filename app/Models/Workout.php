<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = [
        'user_id',
        'routine_day_id',
        'started_at',
        'ended_at',
        'duration_sec',
        'intensity',
        'estimated_kcal',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_sec' => 'integer',
        'estimated_kcal' => 'float',
    ];

    public function sets()
    {
        return $this->hasMany(WorkoutSet::class);
    }
}