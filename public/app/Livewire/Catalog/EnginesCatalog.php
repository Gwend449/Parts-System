<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use App\Models\Engine;

class EnginesCatalog extends Component
{
    public $brand = '';
    public $price_from = null;
    public $price_to = null;

    public $selectedEngine = null;
    public $showModal = false;

    public function mount()
    {

    }

    public function updated()
    {

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
            ->when($this->brand, fn($q) => $q->where('brand', $this->brand))
            ->when($this->price_from, fn($q) => $q->where('price', '>=', $this->price_from))
            ->when($this->price_to, fn($q) => $q->where('price', '<=', $this->price_to))
            ->orderBy('title')
            ->get();

        return view('livewire.catalog.engines-catalog', [
            'engines' => $engines
        ]);
    }
}
