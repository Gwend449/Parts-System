<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Engine extends Model {
    protected $fillable = ['model_id', 'name', 'code', 'volume', 'power', 'price'];
    public function model() {
        return $this->belongsTo(CarModel::class);
    }
}
