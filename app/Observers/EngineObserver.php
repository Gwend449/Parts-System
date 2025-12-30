<?php

namespace App\Observers;

use App\Models\Engine;
use Illuminate\Http\UploadedFile;

class EngineObserver
{
   /**
    * Handle the Engine "created" event.
    */
   public function created(Engine $engine): void
   {
      $this->processImages($engine);
   }

   /**
    * Handle the Engine "updated" event.
    */
   public function updated(Engine $engine): void
   {
      $this->processImages($engine);
   }

   /**
    * Обрабатываем загруженные изображения
    */
   protected function processImages(Engine $engine): void
   {
      $files = request()->file('images');

      if ($files && is_array($files)) {
         foreach ($files as $file) {
            // Обработка UploadedFile объектов
            if ($file instanceof UploadedFile) {
               $engine->addMedia($file)
                  ->toMediaCollection('images');
            }
         }
      }

      // Очищаем кэш изображений после обновления
      $cacheKey = 'engine_images_' . $engine->id;
      \Illuminate\Support\Facades\Cache::forget($cacheKey);
   }
}
