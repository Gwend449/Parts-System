<?php

namespace App\Imports;

use App\Models\Engine;
use Maatwebsite\Excel\Concerns\ToModel;

class EnginesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Пропускаем первую строку (заголовки)
        if ($row[0] === 'Уникальный идентификатор объявления') {
            return null;
        }

        return new Engine([
            'slug'        => $row[0],   // A
            'title'       => $row[11],  // L
            'price'       => $this->toPrice($row[9]), // J
            'brand'       => $row[20],  // U
            'oem'         => $row[21],  // V
            'fit_for'     => $row[24],  // Y
            'description' => $this->clean($row[8]), // I
        ]);
    }

    private function clean($html)
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($html)));
    }

    private function toPrice($value)
    {
        return floatval(preg_replace('/[^\d\.]/', '', $value));
    }
}
