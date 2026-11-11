<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'foods';

    
    protected $fillable = [
        'name','kcal_100','protein_100','carbs_100','fat_100'
    ];
    
}