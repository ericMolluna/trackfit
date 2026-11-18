<?php

namespace App\Models;

use App\Models\Concerns\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use App\Models\Food;

class FoodLog extends Model
{
    use HasEncryptedRouteKey;

    protected $fillable = ['user_id','date','meal','food_id','grams'];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}

