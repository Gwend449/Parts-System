<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckImageSupport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:check-support';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверить поддержку обработки изображений на сервере';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('');
        $this->line('════════════════════════════════════════════');
        $this->line('📸 ПРОВЕРКА ПОДДЕРЖКИ ОБРАБОТКИ ИЗОБРАЖЕНИЙ');
        $this->line('════════════════════════════════════════════');
        $this->line('');

        // PHP версия
        $this->info('PHP версия: ' . phpversion());
        $this->line('');

        // GD расширение
        if (extension_loaded('gd')) {
            $this->info('✅ GD расширение загружено');
        } else {
            $this->error('❌ GD расширение НЕ загружено');
            return 1;
        }

        $this->line('');
        $this->line('📋 Информация о GD:');
        $this->line('');

        $gdInfo = gd_info();
        foreach ($gdInfo as $key => $value) {
            if (is_bool($value)) {
                $status = $value ? '✅ Да' : '❌ Нет';
            } else {
                $status = $value;
            }
            $this->line("  • $key: $status");
        }

        $this->line('');
        $this->line('🖼️  Поддержка форматов изображений:');
        $this->line('');

        $formats = [
            'JPEG (imagejpeg)' => 'imagejpeg',
            'PNG (imagepng)' => 'imagepng',
            'WebP (imagewebp)' => 'imagewebp',
            'GIF (imagegif)' => 'imagegif',
        ];

        foreach ($formats as $name => $function) {
            $supported = function_exists($function) ? '✅' : '❌';
            $this->line("  $supported $name");
        }

        $this->line('');
        $this->line('════════════════════════════════════════════');
        $this->line('');

        return 0;
    }
}
