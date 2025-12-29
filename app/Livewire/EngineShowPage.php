<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Engine;

class EngineShowPage extends Component
{
    public Engine $engine;

    protected $listeners = ['openEngineModal'];

    public function openModal()
    {
        $this->dispatch('openEngineModal', $this->engine->id);
    }

    public function render()
    {
        return view('livewire.engine-show-page');
    }
}
