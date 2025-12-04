<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Engine;

class EnginesCatalog extends Component
{
    use WithPagination;
    public $brand = '';
    public $price_from = null;
    public $price_to = null;
    public $filters = [
        'brand' => null,
        'price_from' => null,
        'price_to' => null,
    ];

    public $selectedEngine = null;
    public $showModal = false;

    public function mount()
    {

    }

    public function applyFilters()
    {
        $this->filters = [
            'brand' => $this->brand,
            'price_from' => $this->price_from,
            'price_to' => $this->price_to,
        ];
        $this->resetPage(); // пагинация начинается с 1 страницы
    }

    public function openModal($engineId)
    {
        $this->selectedEngine = Engine::find($engineId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        $engines = Engine::query()
            ->when($this->filters['brand'], fn($q) => $q->where('brand', $this->filters['brand']))
            ->when($this->filters['price_from'], fn($q) => $q->where('price', '>=', $this->filters['price_from']))
            ->when($this->filters['price_to'], fn($q) => $q->where('price', '<=', $this->filters['price_to']))
            ->orderBy('title')
            ->paginate(20);


        return view('livewire.catalog.engines-catalog', [
            'engines' => $engines
        ]);
    }
}
