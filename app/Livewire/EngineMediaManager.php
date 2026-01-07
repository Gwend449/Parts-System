<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Engine;
use Illuminate\Validation\ValidationException;

class EngineMediaManager extends Component
{
   use WithFileUploads;

   public Engine $engine;
   public $images = [];
   public $uploadedFiles = [];

   #[\Livewire\Attributes\Modelable]
   public $title = '';

   public function mount(Engine $engine)
   {
      $this->engine = $engine;
      $this->loadImages();
   }

   /**
    * Загружаем все фото мотора
    */
   public function loadImages()
   {
      // Получаем только медиа из MediaLibrary (которые можно удалить)
      $this->images = $this->engine->getMedia('images')->map(function ($media) {
         try {
            // Пробуем получить конверсию thumb, если нет - берем оригинал
            $thumbUrl = $media->getUrl('thumb');
         } catch (\Exception $e) {
            $thumbUrl = $media->getUrl();
         }

         return [
            'id' => $media->id,
            'name' => $media->file_name,
            'thumb' => $thumbUrl,
            'url' => $media->getUrl(),
            'size' => round($media->size / 1024 / 1024, 2), // в MB
         ];
      })->toArray();

      \Log::info('EngineMediaManager loaded ' . count($this->images) . ' images for engine ' . $this->engine->id);
   }

   /**
    * Загружаем новые файлы
    */
   public function saveMedia()
   {
      if (empty($this->uploadedFiles)) {
         session()->flash('warning', 'Выберите файлы');
         return;
      }

      // Валидация
      $this->validate([
         'uploadedFiles.*' => 'required|file|mimes:jpeg,png,webp|max:5120', // 5 MB
      ], [
         'uploadedFiles.*.mimes' => 'Допустимые форматы: JPG, PNG, WEBP',
         'uploadedFiles.*.max' => 'Размер файла не должен превышать 5 MB',
      ]);

      try {
         // Проверяем количество файлов
         $currentImages = count($this->images);
         $newFiles = count($this->uploadedFiles);

         if ($currentImages + $newFiles > 6) {
            throw new \Exception('Максимум 6 фотографий. Текущих: ' . $currentImages . ', попытка загрузить: ' . $newFiles);
         }

         \Log::info('Starting media upload for engine ' . $this->engine->id, [
            'current_images' => $currentImages,
            'new_files' => $newFiles,
         ]);

         foreach ($this->uploadedFiles as $file) {
            \Log::info('Adding media file', [
               'filename' => $file->getClientOriginalName(),
               'size' => $file->getSize(),
               'mime' => $file->getMimeType(),
            ]);

            $media = $this->engine->addMedia($file)
               ->toMediaCollection('images');

            \Log::info('Media added successfully', [
               'media_id' => $media->id,
               'url' => $media->getUrl(),
            ]);
         }

         // Очищаем загруженные файлы
         $this->uploadedFiles = [];

         // Перезагружаем список фото
         $this->loadImages();

         // Очищаем кэш
         \Cache::forget('engine_images_' . $this->engine->id);

         session()->flash('success', 'Фотографии загружены успешно! (' . $newFiles . ' файлов)');
         $this->dispatch('photos-updated');
      } catch (ValidationException $e) {
         session()->flash('error', 'Ошибка валидации: ' . collect($e->errors())->flatten()->first());
      } catch (\Exception $e) {
         \Log::error('Error uploading media: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
         ]);
         session()->flash('error', 'Ошибка при загрузке: ' . $e->getMessage());
      }
   }

   /**
    * Удаляем фото по ID
    */
   public function deleteImage($mediaId)
   {
      try {
         $media = $this->engine->getMedia('images')->find($mediaId);

         if ($media) {
            $filename = $media->file_name ?? 'файл';
            $media->delete();

            // Очищаем кэш
            \Cache::forget('engine_images_' . $this->engine->id);

            // Перезагружаем список
            $this->loadImages();

            session()->flash('success', '✓ Фотография удалена: ' . $filename);
            $this->dispatch('photo-deleted');
         }
      } catch (\Exception $e) {
         \Log::error('Error deleting media: ' . $e->getMessage());
         session()->flash('error', '✗ Ошибка при удалении: ' . $e->getMessage());
      }
   }

   /**
    * Отмена загрузки файла
    */
   public function removeUploadedFile($key)
   {
      unset($this->uploadedFiles[$key]);
   }

   public function render()
   {
      return view('livewire.engine-media-manager');
   }
}
