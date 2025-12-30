<?php

namespace App\Console\Commands;

use App\Models\Engine;
use Illuminate\Console\Command;

class TestImageConversions extends Command
{
   protected $signature = 'images:test-conversions {engine_id?}';
   protected $description = 'Тестирует конверсии изображений для мотора';

   public function handle()
   {
      $engineId = $this->argument('engine_id');

      if (!$engineId) {
         // Берем первый мотор с изображениями
         $engine = Engine::whereHas('media', function ($query) {
            $query->where('collection_name', 'images');
         })->first();

         if (!$engine) {
            $this->error('❌ Нет моторов с изображениями!');
            return 1;
         }

         $this->info("📌 Тестирование мотора ID: {$engine->id} ({$engine->title})");
      } else {
         $engine = Engine::findOrFail($engineId);
         $this->info("📌 Тестирование мотора ID: {$engine->id} ({$engine->title})");
      }

      $media = $engine->getMedia('images');

      if ($media->isEmpty()) {
         $this->error('❌ У этого мотора нет загруженных изображений!');
         return 1;
      }

      $this->info("\n📸 Найдено изображений: {$media->count()}\n");

      foreach ($media as $index => $item) {
         $this->line("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
         $imageNum = $index + 1;
         $this->line("📷 Изображение #{$imageNum}: {$item->file_name}");
         $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

         // Информация о файле
         $this->line("   Размер: " . $this->formatBytes($item->size));
         $this->line("   MIME: {$item->mime_type}");
         $this->line("   Дата загрузки: {$item->created_at->format('d.m.Y H:i:s')}");

         // URL оригинального файла
         $originalUrl = $item->getUrl();
         $this->line("\n   ✓ Оригинал:");
         $this->line("     URL: {$originalUrl}");

         // Проверяем конверсии
         $conversions = ['thumb', 'preview'];
         foreach ($conversions as $conversion) {
            $url = $item->getUrl($conversion);
            $fullPath = public_path('storage') . parse_url($url, PHP_URL_PATH);

            $size = file_exists($fullPath) ? filesize($fullPath) : 'N/A';
            $sizeFormatted = is_int($size) ? $this->formatBytes($size) : $size;

            $this->line("\n   ✓ Конверсия '{$conversion}' (размер: {$sizeFormatted}):");
            $this->line("     URL: {$url}");
         }
      }

      $this->line("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n");

      // Проверяем кэш
      $cacheKey = 'engine_images_' . $engine->id;
      $cached = \Illuminate\Support\Facades\Cache::has($cacheKey);

      if ($cached) {
         $this->info("✅ Кэш изображений активен (будет обновлен через 24 часа)");
         $this->line("   Ключ кэша: {$cacheKey}");
      } else {
         $this->warn("⚠️  Кэш изображений еще не создан");
         $this->line("   (будет создан при первом обращении к getAllImages())");
      }

      $this->info("\n✅ Проверка завершена!\n");

      return 0;
   }

   private function formatBytes($bytes, $precision = 2): string
   {
      $units = ['B', 'KB', 'MB', 'GB'];
      $bytes = max($bytes, 0);
      $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
      $pow = min($pow, count($units) - 1);
      $bytes /= (1 << (10 * $pow));

      return round($bytes, $precision) . ' ' . $units[$pow];
   }
}
