<?php

namespace App\Observers;

use App\Models\Engine;
use Illuminate\Http\UploadedFile;

class EngineObserver
{
   /**
    * Handle the Engine "saved" event.
    * Используем saved вместо created/updated чтобы обработать файлы после сохранения модели
    */
   public function saved(Engine $engine): void
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
         $processedCount = 0;
         foreach ($files as $file) {
            // Обработка UploadedFile объектов
            if ($file instanceof UploadedFile && $file->isValid()) {
               try {
                  $engine->addMedia($file)
                     ->toMediaCollection('images');
                  $processedCount++;
                  \Log::info('Added media file to engine ' . $engine->id . ': ' . $file->getClientOriginalName());
               } catch (\Exception $e) {
                  \Log::error('Error adding media to engine ' . $engine->id . ': ' . $e->getMessage());
               }
            }
         }
         
         if ($processedCount > 0) {
            \Log::info('Processed ' . $processedCount . ' image(s) for engine ' . $engine->id);
         }
      } else {
         // Логируем если файлы не найдены (для отладки)
         if (request()->has('images')) {
            \Log::debug('Engine ' . $engine->id . ': images field exists but files array is empty or invalid');
         }
      }

      // Очищаем кэш изображений после обновления
      $cacheKey = 'engine_images_' . $engine->id;
      \Illuminate\Support\Facades\Cache::forget($cacheKey);
   }
}
