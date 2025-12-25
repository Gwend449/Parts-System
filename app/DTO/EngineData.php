<?php

namespace App\DTO;

class EngineData
{
    public function __construct(
        public string $slug,
        public ?string $title,
        public float $price,
        public string $brand,
        public string $oem,
        public string $fit_for,
        public string $description,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
        slug: $data['slug'] ?? $data[0] ?? '',
        title: $data['title'] ?? $data[1] ?? null,
        price: (float)($data['price'] ?? $data[2] ?? 0),
        brand: $data['brand'] ?? $data[3] ?? '',
        oem: $data['oem'] ?? $data[4] ?? '',
        fit_for: $data['fit_for'] ?? $data[5] ?? '',
        description: $data['description'] ?? $data[6] ?? '',
    );
    }

    public function toArray(): array
    {
        return [
            'slug'        => $this->slug,
            'title'       => $this->title,
            'price'       => $this->price,
            'brand'       => $this->brand,
            'oem'         => $this->oem,
            'fit_for'     => $this->fit_for,
            'description' => $this->description,
        ];
    }
}
