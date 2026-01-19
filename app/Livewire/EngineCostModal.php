<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Engine;
use App\Services\AmoService;
use Illuminate\Support\Facades\Log;

class EngineCostModal extends Component
{
    public $showModal = false;
    public $selectedEngine = null;
    public $name;
    public $email;
    public $phone;
    public $errorMessage = null;
    public $successMessage = null;

    #[On('openEngineModal')]
    public function openEngineModal($engineId)
    {
        $this->selectedEngine = Engine::find($engineId);
        $this->showModal = true;
        $this->errorMessage = null;
        $this->successMessage = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->name = null;
        $this->email = null;
        $this->phone = null;
        $this->errorMessage = null;
        $this->successMessage = null;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns',
            'phone' => 'required|string|max:255',
        ]);

        try {
            // Отправляем в AmoCRM
            $amoService = app(AmoService::class);

            $engineTitle = $this->selectedEngine ? $this->selectedEngine->title : 'Неизвестный двигатель';
            $comment = "Запрос стоимости двигателя: {$engineTitle}";

            $leadId = $amoService->sendLead(
                name: $this->name,
                phone: $this->phone,
                email: $this->email,
                brand: null,
                model: null,
                comment: $comment,
                source: 'Запрос стоимости двигателя'
            );

            Log::info('Запрос стоимости двигателя отправлен в AmoCRM', [
                'lead_id' => $leadId,
                'engine_id' => $this->selectedEngine?->id,
                'engine_title' => $engineTitle,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            $this->successMessage = 'Спасибо! Ваш запрос отправлен. Мы свяжемся с вами в ближайшее время.';

            // Очищаем форму
            $this->name = null;
            $this->email = null;
            $this->phone = null;

            // Отправляем событие успеха
            $this->dispatch('requestSent');

            // Закрываем модальное окно через 3 секунды после успешной отправки
            $this->dispatch('closeModalAfterSuccess');

            // Автоматически закрываем через 3 секунды
            $this->dispatch('closeModalAfterDelay');

        } catch (\Exception $e) {
            Log::error('Ошибка при отправке запроса стоимости двигателя в AmoCRM', [
                'error' => $e->getMessage(),
                'engine_id' => $this->selectedEngine?->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            $this->errorMessage = 'Произошла ошибка при отправке запроса. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.';
        }
    }

    public function render()
    {
        return view('livewire.engine-cost-modal');
    }
}
