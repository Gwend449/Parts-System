<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $fillable = ['brand_id', 'name', 'slug'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function engines()
    {
        return $this->hasMany(Engine::class, 'car_model_id');
    }
}
