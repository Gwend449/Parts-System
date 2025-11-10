<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Engine extends Model
{
    protected $fillable = ['car_model_id', 'name', 'code', 'volume', 'power', 'price'];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
}
