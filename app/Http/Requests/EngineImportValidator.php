<?php

namespace App\Http\Requests;

use App\DTO\EngineData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EngineImportValidator
{
    public function validate(EngineData $dto): void
    {
        $validator = Validator::make([
            'slug'   => $dto->slug,
            'title'  => $dto->title,
            'price'  => $dto->price,
            'brand'  => $dto->brand,
            'oem'    => $dto->oem,
            'fit_for' => $dto->fit_for,
            'description' => $dto->description,
        ], [
            'slug'  => 'required|string',
            'title' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'brand' => 'required|string',
            'oem'   => 'required|string',
            'fit_for' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
