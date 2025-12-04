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

        foreach ($rows as $index => $row) {
            // Пропускаем заголовки (если нужно)
            if ($index === 0) {
                continue;
            }

            $dto = $normalizer->normalize($row->toArray());

            // Пропускаем строки с price <= 1749
            if (!$dto) {
                continue;
            }

            $validator->validate($dto);

            \App\Models\Engine::updateOrCreate(
                ['slug' => $dto->slug],
                $dto->toArray()
            );
        }
    }

    public function startRow(): int
    {
        return 5;
    }
}
