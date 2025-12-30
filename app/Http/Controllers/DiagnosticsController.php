<?php

namespace App\Http\Controllers;

/**
 * Диагностический контроллер для проверки поддержки изображений
 */
class DiagnosticsController extends Controller
{
    /**
     * Проверяет поддержку обработки изображений на сервере
     * Доступно по: /admin/diagnostics
     */
    public function checkImageSupport()
    {
        $info = [
            'gd_enabled' => extension_loaded('gd'),
            'gd_info' => gd_info(),
            'php_version' => phpversion(),
            'supported_mime_types' => [
                'image/jpeg' => function_exists('imagejpeg'),
                'image/png' => function_exists('imagepng'),
                'image/webp' => function_exists('imagewebp'),
            ],
            'mime_module' => function_exists('mime_content_type') || function_exists('finfo_file'),
        ];

        return view('admin.diagnostics', $info);
    }

    /**
     * API endpoint для проверки (JSON)
     */
    public function apiCheckImageSupport()
    {
        return response()->json([
            'gd_enabled' => extension_loaded('gd'),
            'gd_info' => gd_info(),
            'supported_formats' => [
                'jpeg' => function_exists('imagejpeg'),
                'png' => function_exists('imagepng'),
                'webp' => function_exists('imagewebp'),
            ],
        ]);
    }
}
