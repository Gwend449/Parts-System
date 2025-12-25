<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use App\Models\Engine;

class RecentEngines extends Component
{
    public $latestEngines = [];
    public $showModal = false;
    public $selectedEngine = null;


    public function mount()
    {
        $this->latestEngines = Engine::query()
            ->latest()
            ->take(3) // сколько моторов показываем
            ->get();
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
        return view('livewire.catalog.recent-engines');
    }
}
