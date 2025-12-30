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

        <div class="col-md-3 d-flex flex-column gap-2">
            <button wire:click="applyFilters" class="btn btn-brand-primary w-100">Применить</button>
            <button wire:click="resetFilters" class="btn btn-secondary w-100">Сбросить фильтры</button>
        </div>
    </div>

    <!-- Список моторов -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($engines as $engine)
            <div class="col">
                <div class="card shadow-sm border-0 h-100">
                    <div class="ratio ratio-4x3 rounded-top overflow-hidden bg-white">
                        @php
                            $images = $engine->getAllImages();
                            $imageUrl = isset($images[0]) ? $images[0]['thumb'] : asset('images/placeholder-engine.jpg');
                        @endphp
                        <img src="{{ $imageUrl }}" class="img-fluid w-100 h-100" style="object-fit: contain;"
                            alt="{{ $engine->title }}">
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <p class="text-muted small mb-1">{{ $engine->brand }}</p>
                        <h6 class="fw-bold">{{ $engine->title }}</h6>
                        <p class="fw-bold mt-2 mb-3">{{ number_format($engine->price, 0, ',', ' ') }} ₽</p>
                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('engine.show', $engine->slug) }}"
                                class="btn btn-outline-primary btn-sm w-50">Подробнее</a>
                            <button class="btn bg-font-color btn-sm w-50 text-light"
                                wire:click="$dispatch('openEngineModal', { engineId: {{ $engine->id }} })">
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
</div>