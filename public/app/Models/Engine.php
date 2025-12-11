<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Engine extends Model implements HasMedia
{
    use InteractsWithMedia;
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'slug',
        'title',
        'price',
        'brand',
        'oem',
        'fit_for',
        'description',
    ];
    protected $table = 'engines';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $casts = [
        'images' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public') // диска public достаточно для админки
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function getAllImages(): array
    {
        $images = [];

        $images = [];

        // 1. Из MediaLibrary
        foreach ($this->getMedia('images') as $media) {
            $images[] = $media->getUrl(); // public URL
        }


        // 2. Фото из папки /public/images/engines/{slug}
        $slug = strtolower(trim($this->oem));
        $folder = public_path("images/engines/" . $slug);

        if (is_dir($folder)) {
            foreach (glob($folder . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) as $file) {
                $images[] = "/images/engines/{$slug}/" . basename($file);
            }
        }

        return $images;

    }

    protected static function booted()
    {
        static::saved(function ($engine) {
            $files = request()->file('images');

            if ($files && is_array($files)) {
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $engine->addMedia($file)->toMediaCollection('images');
                    }
                }
            }
        });
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
