@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-dark text-white py-5">
    <div class="container-lg">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-warning mb-4">Моторы для любых моделей авто</h1>
                <p class="lead text-light mb-4">Подберите двигатель под свою марку за 30 секунд</p>
                <a href="{{ route('catalog') }}" class="btn btn-warning btn-lg fw-bold">Выбрать мотор</a>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="/images/car-engine-motor.jpg" alt="Engine" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Brand Grid -->
<section class="py-5 bg-light">
    <livewire:catalog.brand-selector />
</section>

</section>

<!-- Recent Motors -->
<section class="py-5">
    <div class="container-lg">
        <h2 class="fs-2 fw-bold mb-5">Недавно добавленные моторы</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 border-2 shadow-sm hover-shadow">
                    <img src="public/car-engine-.jpg" class="card-img-top" alt="Motor"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <p class="text-warning fw-bold">BMW</p>
                        <h5 class="card-title">N55B30 Twin Power Turbo</h5>
                        <p class="card-text text-muted">Объем: 3.0L | Мощность: 340 л.с.</p>
                        <button class="btn btn-warning w-100 fw-bold">Узнать стоимость</button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-2 shadow-sm">
                    <img src="public/car-engine-.jpg" class="card-img-top" alt="Motor"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <p class="text-warning fw-bold">TOYOTA</p>
                        <h5 class="card-title">2JZ-GTE Twin Turbo</h5>
                        <p class="card-text text-muted">Объем: 3.0L | Мощность: 330 л.с.</p>
                        <button class="btn btn-warning w-100 fw-bold">Узнать стоимость</button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-2 shadow-sm">
                    <img src="public/car-engine-.jpg" class="card-img-top" alt="Motor"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <p class="text-warning fw-bold">HONDA</p>
                        <h5 class="card-title">K20Z3 VTEC Engine</h5>
                        <p class="card-text text-muted">Объем: 2.0L | Мощность: 200 л.с.</p>
                        <button class="btn btn-warning w-100 fw-bold">Узнать стоимость</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Company -->
<section class="py-5 bg-light">
    <div class="container-lg">
        <h2 class="fs-2 fw-bold mb-5">О компании</h2>
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <p class="lead text-dark mb-4">
                    Компания начала свою работу в 2012 году. Мы осуществляем прямые поставки контрактных БУ
                    автозапчастей из Японии.
                </p>
                <ul class="list-unstyled space-y-3">
                    <li class="d-flex align-items-center mb-3">
                        <span class="text-warning fw-bold me-3">◆</span>
                        <span>Без пробега по РФ</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <span class="text-warning fw-bold me-3">◆</span>
                        <span>Полный пакет документов</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <span class="text-warning fw-bold me-3">◆</span>
                        <span>Расширенная гарантия</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <img src="public/engine-parts-warehouse.jpg" alt="Company" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>
@endsection
