<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ← NUEVO

class RoutineDay extends Model
{
    use HasFactory, SoftDeletes; // ← NUEVO: SoftDeletes

    protected $fillable = [
        'routine_id',
        'day',
        'position',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function exercises()
    {
        return $this->hasMany(RoutineExercise::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}
