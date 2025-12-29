<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use App\Models\Engine;

class RecentEngines extends Component
{
    public $latestEngines = [];

    public function mount()
    {
        $this->latestEngines = Engine::query()
            ->latest()
            ->take(3) // сколько моторов показываем
            ->get();
    }

    public function render()
    {
        return view('livewire.catalog.recent-engines');
    }
}
