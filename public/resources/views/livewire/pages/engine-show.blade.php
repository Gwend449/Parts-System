@extends('layouts.app')
<style>
    body {
        background-color: #f8f9fa;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1a3a6b;
        border-bottom: 3px solid #1a3a6b;
        padding-bottom: 0.75rem;
        margin-bottom: 2rem;
    }

    .page-title .text-danger {
        color: #d32f2f !important;
    }

    .product-image {
        border: 1px solid #ddd;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .thumbnail-gallery {
        display: flex;
        gap: 0.75rem;
    }

    .thumbnail-gallery img {
        width: 80px;
        height: 80px;
        border: 1px solid #ddd;
        border-radius: 0.375rem;
        cursor: pointer;
        object-fit: cover;
        transition: border-color 0.2s;
    }

    .thumbnail-gallery img:hover {
        border-color: #1a3a6b;
    }

    .specs-table {
        background-color: white;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
    }

    .specs-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .specs-table td:first-child {
        color: #666;
        font-weight: 500;
        width: 45%;
    }

    .specs-table td:last-child {
        color: #333;
        font-weight: 600;
    }

    .specs-table tr:last-child td {
        border-bottom: none;
    }

    .price-section {
        margin-bottom: 1.5rem;
    }

    .price-label {
        color: #666;
        font-size: 0.95rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    .price-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a3a6b;
    }

    .price-value .text-danger {
        color: #d32f2f !important;
    }

    .btn-primary-custom {
        background-color: #1a3a6b;
        border-color: #1a3a6b;
        color: white;
        font-weight: 600;
        padding: 0.875rem 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: background-color 0.2s;
    }

    .btn-primary-custom:hover {
        background-color: #132851;
        border-color: #132851;
        color: white;
    }

    .price-hint {
        color: #666;
        font-size: 0.9rem;
        margin-top: 1rem;
    }

    .info-section {
        background-color: white;
        padding: 3rem 2rem;
        margin-top: 3rem;
        border-top: 1px solid #e0e0e0;
    }

    .info-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a3a6b;
        margin-bottom: 2rem;
    }

    .info-box {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-icon {
        flex-shrink: 0;
        width: 60px;
        height: 60px;
        background-color: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a3a6b;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .info-content h5 {
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .info-content p {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
        margin: 0;
    }
</style>
@section('content')
    <div class="container-lg py-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-warning text-decoration-none">Главная</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('catalog') }}" class="text-warning text-decoration-none">Каталог моторов</a>
                </li>
                <li class="breadcrumb-item active">{{ $engine->title }}</li>
            </ol>
        </nav>

        <!-- Main Row -->
        <div class="row g-5 mb-5">

            <!-- Left Column — Images -->
            <div class="col-lg-6">
                <img src="{{ $engine->image ?? '/images/placeholder-engine.png' }}"
                    class="img-fluid product-image border rounded mb-3" alt="{{ $engine->title }}" id="mainImage">

                <!-- Gallery thumbnails -->
                <div class="thumbnail-gallery d-flex gap-2">
                    <img src="{{ $engine->image ?? '/images/placeholder-engine.png' }}" class="img-thumbnail" width="80"
                        onclick="document.getElementById('mainImage').src=this.src">

                    @if($engine->image_2)
                        <img src="{{ $engine->image_2 }}" class="img-thumbnail" width="80"
                            onclick="document.getElementById('mainImage').src=this.src">
                    @endif

                    @if($engine->image_3)
                        <img src="{{ $engine->image_3 }}" class="img-thumbnail" width="80"
                            onclick="document.getElementById('mainImage').src=this.src">
                    @endif
                </div>
            </div>

            <!-- Right Column — Details -->
            <div class="col-lg-6">

                <!-- Specs Table -->
                <table class="specs-table w-100 mb-4">
                    <tr>
                        <td>Марка</td>
                        <td>{{ $engine->brand }}</td>
                    </tr>
                    <tr>
                        <td>OEM</td>
                        <td>{{ $engine->oem }}</td>
                    </tr>
                    <tr>
                        <td>Тип двигателя</td>
                        <td>{{ $engine->engine_type ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Объем ДВС</td>
                        <td>{{ $engine->volume ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Мощность (Л.С.)</td>
                        <td>{{ $engine->horsepower ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Модель авто</td>
                        <td>{{ $engine->model ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Год выпуска</td>
                        <td>{{ $engine->years ?? '—' }}</td>
                    </tr>
                </table>

                <!-- Price Section -->
                <div class="price-section mb-3">
                    <span class="price-label text-muted d-block">Предзаказ</span>
                    <div class="price-value fs-3 fw-bold">
                        от <span class="text-danger">{{ number_format($engine->price, 0, ',', ' ') }}</span> ₽
                    </div>
                </div>

                <p class="text-muted mb-3">
                    Узнайте актуальную цену на {{ $engine->title }} прямо сейчас
                </p>

                <!-- CTA Button -->
                <button class="btn btn-warning btn-lg w-100 mb-4"
                    onclick="Livewire.emit('openCostModal', {{ $engine->id }})">
                    Узнать стоимость
                </button>

            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">

            <h3 class="fw-bold mb-4 text-uppercase">
                {{ $engine->title }} в Нижнем Новгороде
            </h3>

            <div class="row g-4">
                <div class="col-lg-6">

                    <div class="info-box d-flex mb-3">
                        <div class="info-icon fs-2 me-3">📋</div>
                        <div>
                            <h5>Полный пакет документов</h5>
                            <p class="text-muted">Каждый агрегат имеет при себе все необходимые документы</p>
                        </div>
                    </div>

                    <div class="info-box d-flex mb-3">
                        <div class="info-icon fs-2 me-3">30</div>
                        <div>
                            <h5>Гарантия на ДВС и КПП до 30 дней</h5>
                            <p class="text-muted">В течение гарантийного срока вы можете обменять товар</p>
                        </div>
                    </div>

                    <div class="info-box d-flex mb-3">
                        <div class="info-icon fs-2 me-3">⚙️</div>
                        <div>
                            <h5>Комплексная диагностика</h5>
                            <p class="text-muted">Проводим полную проверку бу моторов</p>
                        </div>
                    </div>

                </div>

                <div class="col-lg-6">
                    <p class="text-muted" style="line-height: 1.6;">
                        {{ $engine->description ?: 'Описание будет добавлено позже.' }}
                    </p>

                    <p class="text-muted" style="line-height: 1.6;">
                        Совместимость: {{ $engine->fit_for ?: 'Информация уточняется.' }}
                    </p>

                    <p class="text-muted" style="line-height: 1.6; margin-top: 1rem;">
                        Мы поставляем проверенные моторы без пробега по РФ, снятые с автомобилей из США, Европы, Кореи и
                        Японии.
                    </p>
                </div>
            </div>

        </div>

    </div>
@endsection
