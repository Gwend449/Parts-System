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
                
                // Очищаем кэш изображений после удаления
                $cacheKey = 'engine_images_' . $this->id;
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
                
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
     * Включает только медиа из MediaLibrary (которые можно удалить)
     */
    public function getMediaList()
    {
        $mediaCollection = $this->getMedia('images');
        
        \Log::info('getMediaList for engine ' . $this->id, [
            'media_count' => $mediaCollection->count(),
            'oem' => $this->oem
        ]);
        
        $mediaList = $mediaCollection->map(function ($media) {
            try {
                $thumbUrl = $media->getUrl('thumb');
            } catch (\Exception $e) {
                // Если конверсия не существует, используем оригинал
                $thumbUrl = $media->getUrl();
            }
            
            return [
                'id' => $media->id,
                'name' => $media->file_name,
                'url' => $media->getUrl(),
                'thumb' => $thumbUrl,
                'size' => $media->size,
                'type' => 'uploaded', // Из MediaLibrary
            ];
        })->toArray();
        
        // Также добавляем изображения из папки public/images/engines/{oem}
        // Но только для информации (их нельзя удалить через админку)
        $slug = strtolower(trim($this->oem));
        $folder = public_path("images/engines/" . $slug);
        
        if (is_dir($folder)) {
            $folderImages = glob($folder . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
            \Log::info('getMediaList: Found folder images', [
                'engine_id' => $this->id,
                'folder' => $folder,
                'count' => count($folderImages),
                'exists' => is_dir($folder)
            ]);
            
            foreach ($folderImages as $file) {
                if (is_file($file)) {
                    $filename = basename($file);
                    $mediaList[] = [
                        'id' => null, // Нет ID, так как это не из MediaLibrary
                        'name' => $filename,
                        'url' => "/images/engines/{$slug}/{$filename}",
                        'thumb' => "/images/engines/{$slug}/{$filename}",
                        'size' => filesize($file),
                        'type' => 'folder', // Из папки, нельзя удалить
                    ];
                }
            }
        } else {
            \Log::info('getMediaList: Folder does not exist', [
                'engine_id' => $this->id,
                'folder' => $folder,
                'oem' => $this->oem
            ]);
        }
        
        return $mediaList;
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
