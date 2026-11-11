<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionGoal extends Model
{
    protected $fillable = [
        'user_id','calories','protein_g','carbs_g','fat_g'
    ];
}
