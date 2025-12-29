<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Engine;

class EngineCostModal extends Component
{
    public $showModal = false;
    public $selectedEngine = null;
    public $name;
    public $phone;

    #[On('openEngineModal')]
    public function openEngineModal($engineId)
    {
        $this->selectedEngine = Engine::find($engineId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->name = null;
        $this->phone = null;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        // Логика отправки в amoCRM или сохранения
        // Example: $this->sendToAmoCRM($this->selectedEngine, $this->name, $this->phone);

        $this->closeModal();
        $this->dispatch('requestSent'); // событие успеха, можно слушать на родителе
    }

    public function render()
    {
        return view('livewire.engine-cost-modal');
    }
}
