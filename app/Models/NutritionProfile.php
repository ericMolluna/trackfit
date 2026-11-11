<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionProfile extends Model
{
    protected $fillable = ['user_id','sex','age','height_cm','weight_kg','activity','goal'];

}
