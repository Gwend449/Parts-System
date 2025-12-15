<?php

namespace App\Imports;

use App\Services\EngineImportNormalizer;
use App\Http\Requests\EngineImportValidator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;


class EnginesImport implements ToCollection, WithStartRow
{
    public function collection(Collection $rows)
    {
        $normalizer = new EngineImportNormalizer();
        $validator = new EngineImportValidator();

        // 1. Собираем все строки
        foreach ($rows as $index => $row) {
            if ($index === 0)
                continue;
            $normalizer->normalize($row->toArray());
        }

        // 2. Получаем уникальные моторы
        foreach ($normalizer->getUniqueEngines() as $dto) {

            $validator->validate($dto);

            \App\Models\Engine::updateOrCreate(
                [
                    'brand' => $dto->brand,
                    'oem' => $dto->oem,
                ],
                $dto->toArray()
            );
        }
    }

    public function startRow(): int
    {
        return 5;
    }
}
