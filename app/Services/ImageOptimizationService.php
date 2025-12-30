<?php

namespace App\Services;

/**
 * Сервис для управления конфигурацией оптимизации изображений
 * 
 * Используется для хранения констант и конфигурации
 */
class ImageOptimizationService
{
   /**
    * Максимальные размеры для конверсий (превью)
    * Значительно уменьшает размер файлов для каталога
    */
   public const CONVERSIONS = [
      'thumb' => ['width' => 250, 'height' => 250],   // для списка
      'preview' => ['width' => 600, 'height' => 600], // для просмотра товара
   ];

   /**
    * Качество изображений (0-100)
    * 75 - хороший баланс между качеством и размером
    */
   public const QUALITY = 75;

   /**
    * Вспомогательный метод для получения конфигурации
    */
   public static function getConversions(): array
   {
      return self::CONVERSIONS;
   }

   /**
    * Вспомогательный метод для получения качества
    */
   public static function getQuality(): int
   {
      return self::QUALITY;
   }
}
