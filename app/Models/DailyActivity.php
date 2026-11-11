<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'steps',
        'steps_kcal',
    ];

    protected $casts = [
    'steps' => 'integer',
    'steps_kcal' => 'float',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}