<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Engine extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'engines';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];

    public function images()
    {
        return $this->hasMany(EngineImage::class)->orderBy('sort_order');
    }

    public function getAllImages(): array
    {
        $images = [];

        // 1. Фото из базы
        foreach ($this->images as $img) {
            $images[] = asset("storage/{$img->path}");
        }

        // 2. Фото из OEM-папок
        $oemFolder = public_path('engines/' . trim($this->oem));
        if (is_dir($oemFolder)) {
            foreach (scandir($oemFolder) as $file) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $file)) {
                    $images[] = asset("engines/" . trim($this->oem) . "/{$file}");
                }
            }
        }

        return $images;
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
