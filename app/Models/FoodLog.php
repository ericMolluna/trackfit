<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Food;

class FoodLog extends Model
{
    protected $fillable = ['user_id','date','meal','food_id','grams'];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}

