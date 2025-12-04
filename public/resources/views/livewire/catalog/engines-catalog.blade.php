<div>
    <!-- Фильтры -->
    <div class="row mb-4 g-3 align-items-end">

        <div class="col-md-3">
            <label class="form-label fw-semibold">Бренд</label>
            <select class="form-select" wire:model="brand">
                <option value="">Все бренды</option>
                @foreach(\App\Models\Engine::select('brand')->distinct()->pluck('brand') as $b)
                    <option value="{{ $b }}">{{ $b }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Цена от</label>
            <input type="number" wire:model="price_from" class="form-control" placeholder="0">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Цена до</label>
            <input type="number" wire:model="price_to" class="form-control" placeholder="100000">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold opacity-0">.</label>
            <button wire:click="applyFilters" class="btn btn-primary w-100">
                Применить
            </button>
        </div>

    </div>


    <!-- Список моторов -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($engines as $engine)
            <div class="col">
                <div class="card shadow-sm border-0 small-card">

                    <img src="/images/placeholder-engine.jpg" class="card-img-top" style="height: 160px; object-fit: cover;"
                        alt="{{ $engine->title }}">

                    <div class="card-body p-3">
                        <p class="text-muted small m-0">{{ $engine->brand }}</p>
                        <h6 class="fw-bold mt-1">{{ $engine->title }}</h6>

                        <p class="fw-bold mt-2 mb-2">
                            {{ number_format($engine->price, 0, ',', ' ') }} ₽
                        </p>

                        <div class="d-flex gap-2">
                            <a href="{{ route('engine.show', $engine->slug) }}" class="btn btn-outline-primary btn-sm w-50">
                                Подробнее
                            </a>

                            <button wire:click="openModal({{ $engine->id }})"
                                class="btn bg-font-color btn-sm w-50 text-light">
                                Стоимость
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-5 text-left">
        {{ $engines->links('pagination::bootstrap-5') }}
    </div>

    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1"
            style="background: rgba(0,0,0,0.55); backdrop-filter: blur(3px);">

            <div class="modal-dialog modal-dialog-centered modal-md">

                <div class="modal-content shadow-lg border-0 rounded-4">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold fs-4">
                            {{ $selectedEngine->title }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body pt-2">
                        <p class="text-muted mb-4">
                            Чтобы узнать стоимость — заполните данные ниже
                        </p>

                        <form wire:submit.prevent="submit" class="mt-2">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ваше имя</label>
                                <input type="text" wire:model="name" class="form-control form-control-lg rounded-3"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Телефон</label>
                                <input type="tel" wire:model="phone" class="form-control form-control-lg rounded-3"
                                    required>
                            </div>

                            <button class="btn btn-warning w-100 fw-bold py-2 rounded-3 fs-6">
                                Отправить запрос
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    @endif


</div>
