<div>
    <div class="container-lg">
        <h2 class="fs-2 fw-bold mb-5">Недавно добавленные моторы</h2>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($latestEngines as $engine)
                <div class="col">
                    <div class="card h-100 border-2 shadow-sm">

                        <!-- Image -->
                        <div class="ratio ratio-4x3 overflow-hidden">
                            <img src="{{ $engine->getAllImages()[0] ?? asset('images/placeholder-engine.jpg') }}"
                                class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $engine->title }}">
                        </div>

                        <!-- Body -->
                        <div class="card-body d-flex flex-column">
                            <p class="text-brand fw-bold mb-1">
                                {{ $engine->brand }}
                            </p>

                            <h5 class="card-title ">
                                {{ $engine->title }}
                            </h5>

                            <p class="card-text text-muted flex-grow-1">
                                Объем: {{ $engine->volume ?? '—' }}
                                |
                                Мощность: {{ $engine->horsepower ?? '—' }} л.с.
                            </p>

                            <button class="btn btn-brand-primary w-100 fw-bold text-light mt-auto"
                                wire:click="$dispatch('openEngineModal', { engineId: {{ $engine->id }} })">
                                Узнать стоимость
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>