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

                        @if($successMessage)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ $successMessage }}
                                <button type="button" class="btn-close" wire:click="closeModal"></button>
                            </div>
                        @elseif($errorMessage)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $errorMessage }}
                                <button type="button" class="btn-close" wire:click="closeModal"></button>
                            </div>
                        @else
                            <form wire:submit.prevent="submit" class="mt-2">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ваше имя <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name"
                                        class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" wire:model="email"
                                        class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                                        required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Телефон <span class="text-danger">*</span></label>
                                    <input type="tel" wire:model="phone"
                                        class="form-control form-control-lg rounded-3 @error('phone') is-invalid @enderror"
                                        required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-brand-primary w-100 fw-bold py-2 rounded-3 fs-6"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Отправить запрос</span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-2"></span>Отправка...
                                    </span>
                                </button>

                            </form>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    @endif
</div>