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
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Регистрирует конверсии (превью) для быстрой загрузки
     * Создает уменьшенные версии изображений для каталога
     */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        // Преобразование для каталога
        $this->addMediaConversion('thumb')
            ->crop(250, 250)
            ->quality(75)
            ->nonQueued();

        // Преобразование для деталей товара
        $this->addMediaConversion('preview')
            ->crop(600, 600)
            ->quality(75)
            ->nonQueued();
    }

    public function getAllImages(): array
    {
        // Кэшируем изображения на 24 часа для быстрой загрузки в каталоге
        $cacheKey = 'engine_images_' . $this->id;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () {
            $images = [];

            // 1. Из MediaLibrary (загруженные через админку)
            foreach ($this->getMedia('images') as $media) {
                // Используем thumb конверсию для быстрой загрузки в каталоге
                $images[] = [
                    'original' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),     // 250x250
                    'preview' => $media->getUrl('preview'),  // 600x600
                    'id' => $media->id,
                    'type' => 'uploaded',
                ];
            }

            // 2. Фото из папки /public/images/engines/{slug}
            $slug = strtolower(trim($this->oem));
            $folder = public_path("images/engines/" . $slug);

            if (is_dir($folder)) {
                foreach (glob($folder . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) as $file) {
                    $filename = basename($file);
                    $images[] = [
                        'original' => "/images/engines/{$slug}/{$filename}",
                        'thumb' => "/images/engines/{$slug}/{$filename}",
                        'preview' => "/images/engines/{$slug}/{$filename}",
                        'id' => null,
                        'type' => 'folder',
                    ];
                }
            }

            return $images;
        });
    }

    /**
     * Удаляет медиа файл по ID
     * Используется для удаления загруженных фотографий
     */
    public function deleteMedia($mediaId): bool
    {
        try {
            $media = $this->getMedia('images')->find($mediaId);
            if ($media) {
                $media->delete();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            \Log::error("Failed to delete media {$mediaId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Получает список всех медиа для отображения в админке
     */
    public function getMediaList()
    {
        return $this->getMedia('images')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->file_name,
                'url' => $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
                'size' => $media->size,
            ];
        })->toArray();
    }

    protected static function booted()
    {
        static::deleted(function ($engine) {
            // Удаляем все медиа при удалении Engine
            $engine->clearMediaCollection('images');
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
