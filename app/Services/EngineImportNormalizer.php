<?php

namespace App\Services;

use App\DTO\EngineData;

class EngineImportNormalizer
{
    private array $brands = [
        'TOYOTA',
        'NISSAN',
        'MAZDA',
        'MITSUBISHI',
        'SUBARU',
        'HONDA',
        'BMW',
        'MERCEDES',
        'AUDI',
        'KIA',
        'HYUNDAI',
        'FORD',
        'VOLKSWAGEN',
        'SUZUKI',
        'OPEL',
        'SSANGYONG',
        'PEUGEOT'

    ];

    private array $seen = [];

    public function normalize(array $row): void
    {
        $price = $this->toPrice($row[9] ?? null);

        if ($price <= 1749) {
            return;
        }

        $brand = $this->detectBrand($row);
        $oem = $this->detectOEM($row);
        $slug = trim($row[0] ?? '');

        // Генерируем корректный ключ уникальности
        $uniqueKey = $this->makeUniqueKey($brand, $oem, $slug);

        $engine = new EngineData(
            slug: $slug,
            title: $row[11] ?? null,
            price: $price,
            brand: $brand,
            oem: $oem,
            fit_for: $this->detectFitFor($row),
            description: $this->cleanHtml($row[8] ?? ''),
        );

        // Логика объединения: минимальная цена
        if (isset($this->seen[$uniqueKey])) {

            if ($engine->price < $this->seen[$uniqueKey]->price) {
                $this->seen[$uniqueKey]->price = $engine->price;
            }

        } else {
            $this->seen[$uniqueKey] = $engine;
        }
    }

    private function makeUniqueKey(string $brand, string $oem, string $slug): string
    {
        if ($oem !== 'NO_OEM') {
            return $brand . '_' . $oem;
        }

        return $brand . '_NOOEM_' . $slug;
    }

    public function getUniqueEngines(): array
    {
        return array_values($this->seen);
    }
    private function cleanHtml(?string $html): string
    {
        $text = strip_tags($html);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function toPrice(?string $value): float
    {
        return floatval(preg_replace('/[^\d\.]/', '', $value));
    }

    private function detectBrand(array $row): string
    {
        $brand = trim($row[20] ?? '');

        if ($brand !== '' && $brand !== 'Не знаю') {
            return strtoupper($brand);
        }

        $title = strtoupper($row[11] ?? '');
        $desc = strtoupper(strip_tags($row[8] ?? ''));

        // 1. Ищем бренд в title
        foreach ($this->brands as $b) {
            if (strpos($title, $b) !== false) {
                return $b;
            }
        }

        // 2. В desc
        foreach ($this->brands as $b) {
            if (strpos($desc, $b) !== false) {
                return $b;
            }
        }

        // 3. По OEM-кодам
        $oem = strtoupper(trim($row[21] ?? ''));

        if ($oem !== '') {
            if (str_starts_with($oem, 'JD'))
                return 'NISSAN';
            if (str_starts_with($oem, 'EJ'))
                return 'SUBARU';
            if (preg_match('/^\dNZ|^\dZR|^2AZ/', $oem))
                return 'TOYOTA';
            if (preg_match('/^4G|^6G/', $oem))
                return 'MITSUBISHI';
        }

        return 'UNKNOWN';
    }

    private function detectOEM(array $row): string
    {
        $oem = trim($row[21] ?? '');

        if ($oem !== '' && $oem !== 'Не знаю') {
            return $oem;
        }

        $title = strtoupper($row[11] ?? '');
        $desc = strtoupper(strip_tags($row[8] ?? ''));

        // Ищем длинные кодовые слова
        if (preg_match('/[A-Z0-9\-]{5,}/', $title, $m)) {
            return $m[0];
        }

        if (preg_match('/[A-Z0-9\-]{5,}/', $desc, $m)) {
            return $m[0];
        }

        return 'NO_OEM';
    }

    private function detectFitFor(array $row): string
    {
        $fit = trim($row[24] ?? '');

        if ($fit !== '') {
            return $fit;
        }

        $desc = strip_tags($row[8] ?? '');

        // Поиск моделей авто
        if (preg_match_all('/([A-Z][a-z]+(?:\s[A-Z0-9\-]+)?)/u', $desc, $m)) {
            return implode(', ', array_unique($m[0]));
        }

        return 'UNSPECIFIED';
    }
}
