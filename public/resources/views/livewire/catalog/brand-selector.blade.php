<div class="container-lg">

    <h2 class="text-center fs-2 fw-bold mb-5">Выберите марку автомобиля</h2>

    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3 mb-4">
        @foreach ($brands as $brand)
            <div class="col">
                <div wire:click="selectBrand('{{ $brand }}')" class="card p-3 text-center h-100"
                    style="cursor: pointer;
                               border-width: 2px;
                               border-style: solid;
                               {{ $selectedBrand === $brand ? 'border-color: #ffc107; background-color: #ffc107; color: #000;' : 'border-color: #dee2e6; color: #000;' }}">

                    <p class="fw-bold mb-0">
                        {{ $brand }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center">
        <button wire:click="goToCatalog" class="btn btn-dark btn-lg fw-bold">
            Найти двигатель
        </button>
    </div>

</div>
