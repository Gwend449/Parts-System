<div>
    @if($showModal && $selectedEngine)
        <div class="modal fade show d-block" tabindex="-1"
             style="background: rgba(0,0,0,0.55); backdrop-filter: blur(3px);">

            <div class="modal-dialog modal-dialog-centered modal-md">

                <div class="modal-content shadow-lg border-0 rounded-4">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold fs-4">{{ $selectedEngine->title }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body pt-2">
                        <p class="text-muted mb-4">
                            Чтобы узнать стоимость — заполните данные ниже
                        </p>

                        <form wire:submit.prevent="submit" class="mt-2">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ваше имя</label>
                                <input type="text" wire:model="name" 
                                       class="form-control form-control-lg rounded-3" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Телефон</label>
                                <input type="tel" wire:model="phone" 
                                       class="form-control form-control-lg rounded-3" required>
                            </div>

                            <button class="btn btn-brand-primary w-100 fw-bold py-2 rounded-3 fs-6">
                                Отправить запрос
                            </button>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    @endif
</div>
