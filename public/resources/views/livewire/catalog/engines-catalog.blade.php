<div>
    <!-- Фильтры -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <select class="form-select" wire:model="brand">
                <option value="">Все бренды</option>
                @foreach(\App\Models\Engine::select('brand')->distinct()->pluck('brand') as $b)
                    <option value="{{ $b }}">{{ $b }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="number" wire:model="price_from" class="form-control" placeholder="Цена от">
        </div>

        <div class="col-md-3">
            <input type="number" wire:model="price_to" class="form-control" placeholder="Цена до">
        </div>
    </div>

    <!-- Список моторов -->
    <div class="row row-cols-1 row-cols-md-2 g-4">
        @foreach($engines as $engine)
            <div class="col">
                <div class="card h-100">

                    <img src="{{ $engine->image ?? '/images/placeholder-engine.png' }}" class="card-img-top"
                        alt="{{ $engine->title }}">

                    <div class="card-body">
                        <p class="text-muted small m-0">{{ $engine->brand }}</p>
                        <h5 class="card-title">{{ $engine->title }}</h5>

                        <p class="card-price fw-bold mt-2">
                            {{ number_format($engine->price, 0, ',', ' ') }} ₽
                        </p>

                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('engine.show', $engine->slug) }}" class="btn btn-primary">
                                Подробнее
                            </a>

                            <button wire:click="openModal({{ $engine->id }})" class="btn btn-warning">
                                Узнать стоимость
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>


    @if($showModal)
        <div class="modal-backdrop fade show"></div>

        <div class="modal d-block" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-2">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Уточнить стоимость – {{ $selectedEngine->title }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <form wire:submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label">Ваше имя</label>
                                <input type="text" class="form-control" wire:model="name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Телефон</label>
                                <input type="tel" class="form-control" wire:model="phone" required>
                            </div>

                            <button class="btn btn-warning w-100">
                                Отправить запрос
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
