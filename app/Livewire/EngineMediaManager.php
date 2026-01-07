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
         return [
            'id' => $media->id,
            'name' => $media->file_name,
            'thumb' => $media->getUrl('thumb') ?? $media->getUrl(),
            'url' => $media->getUrl(),
            'size' => round($media->size / 1024 / 1024, 2), // в MB
         ];
      })->toArray();
   }

   /**
    * Загружаем новые файлы
    */
   public function saveMedia()
   {
      if (empty($this->uploadedFiles)) {
         $this->dispatch('notify', ['type' => 'warning', 'message' => 'Выберите файлы']);
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

         foreach ($this->uploadedFiles as $file) {
            $this->engine->addMedia($file)
               ->toMediaCollection('images');
         }

         // Очищаем загруженные файлы
         $this->uploadedFiles = [];

         // Перезагружаем список фото
         $this->loadImages();

         // Очищаем кэш
         \Cache::forget('engine_images_' . $this->engine->id);

         $this->dispatch('notify', ['type' => 'success', 'message' => 'Фотографии загружены успешно!']);
      } catch (ValidationException $e) {
         $this->dispatch('notify', ['type' => 'error', 'message' => 'Ошибка валидации: ' . collect($e->errors())->flatten()->first()]);
      } catch (\Exception $e) {
         \Log::error('Error uploading media: ' . $e->getMessage());
         $this->dispatch('notify', ['type' => 'error', 'message' => 'Ошибка при загрузке: ' . $e->getMessage()]);
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
            $media->delete();

            // Очищаем кэш
            \Cache::forget('engine_images_' . $this->engine->id);

            // Перезагружаем список
            $this->loadImages();

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Фотография удалена']);
         }
      } catch (\Exception $e) {
         \Log::error('Error deleting media: ' . $e->getMessage());
         $this->dispatch('notify', ['type' => 'error', 'message' => 'Ошибка при удалении: ' . $e->getMessage()]);
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
