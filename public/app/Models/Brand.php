<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Brand extends Model
{
    protected $fillable = ['name', 'slug'];

    public function carModels()
    {
        return $this->hasMany(CarModel::class);
    }
}

