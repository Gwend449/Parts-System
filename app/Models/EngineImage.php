<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngineImage extends Model
{
    protected $fillable = ['engine_id', 'path', 'sort_order'];

    public function engine()
    {
        return $this->belongsTo(Engine::class);
    }
}
