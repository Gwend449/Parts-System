<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $fillable = ['brand_id', 'model_id', 'engine_id', 'name', 'phone', 'comment', 'status'];
    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    public function model() {
        return $this->belongsTo(CarModel::class);
    }
    public function engine() {
        return $this->belongsTo(Engine::class);
    }
}
