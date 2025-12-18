@extends('layouts.app')
@section('content')
    <div class="page-container py-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"
                        class="text-brand text-decoration-none">Главная</a></li>
                <li class="breadcrumb-item"><a href="{{ route('catalog') }}"
                        class="text-brand text-decoration-none">Каталог</a></li>
                <li class="breadcrumb-item active">{{ $engine->title }}</li>
            </ol>
        </nav>

        <div class="row g-5">

            <!-- LEFT COLUMN: Image Gallery -->
            <div class="col-lg-6">
                <div class="border rounded shadow-sm p-3 bg-white">

                    <!-- Main image -->
                    <div class="ratio ratio-1x1 mb-3 bg-white rounded overflow-hidden">
                        <img id="mainImage" src="{{ $engine->getAllImages()[0] ?? asset('images/placeholder-engine.jpg') }}"
                            class="img-fluid w-100 h-100" style="object-fit: contain;" alt="{{ $engine->title }}">
                    </div>

                    <!-- Thumbnails -->
                    <div class="d-flex gap-3 flex-wrap justify-content-start">
                        @foreach($engine->getAllImages() as $img)
                            <img src="{{ $img }}" class="img-thumbnail"
                                style="width:90px;height:90px;object-fit:cover;cursor:pointer;"
                                onclick="document.getElementById('mainImage').src=this.src" alt="">
                        @endforeach
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: Content -->
            <div class="col-lg-6">
                <h1 class="fw-bold mb-4 text-font-color">{{ $engine->title }}</h1>

                <!-- Specs table -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-0">
                        <table class="table table-borderless specs-table-modern mb-0">
                            <tbody>
                                <tr>
                                    <td>Марка</td>
                                    <td class="fw-semibold">{{ $engine->brand }}</td>
                                </tr>
                                <tr>
                                    <td>OEM</td>
                                    <td class="fw-semibold">{{ $engine->oem }}</td>
                                </tr>
                                <tr>
                                    <td>Совместимость</td>
                                    <td class="fw-semibold">{{ $engine->fit_for ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Price Section -->
                <div class="mb-4">
                    <div class="text-muted small">Предзаказ</div>
                    <div class="fs-2 fw-bold text-font-color">от <span
                            class="text-danger">{{ number_format($engine->price, 0, ',', ' ') }}</span> ₽</div>
                    <div class="text-muted">Узнайте актуальную цену на {{ $engine->title }}</div>
                </div>

                <!-- CTA Button -->
                <button class="btn bg-font-color w-75 py-3 mb-4 text-light fw-bold"
                    onclick="Livewire.emit('openCostModal', {{ $engine->id }})">Узнать стоимость</button>

            </div>
        </div>
        <div class="row mt-lg-4 mt-3">

            <!-- Left Column: Description -->
            <div class="col-lg-6 col-12 pe-lg-4 border-lg-end">
                <h4 class="fw-bold text-brand mb-3">Описание</h4>
                <p class="text-muted mb-3" style="line-height:1.6;">
                    {{ $engine->description ?: 'Описание будет добавлено позже.' }}
                </p>
            </div>

            <!-- Right Column: Why Choose Us -->
            <div class="col-lg-6 col-12 ps-lg-4 mt-4 mt-lg-0">
                <h4 class="fw-bold text-brand mb-4">Почему выбирают нас</h4>

                <div class="mb-4">
                    <div class="d-flex align-items-start mb-5">
                        <div class="info-icon me-3 fs-2">📄</div>
                        <div>
                            <h5 class="fw-bold mb-2">Документы в комплекте</h5>
                            <p class="text-muted mb-0 small">
                                Полный комплект документов для каждой поставки.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-5">
                        <div class="info-icon me-3 fs-2">🛠️</div>
                        <div>
                            <h5 class="fw-bold mb-2">Гарантия до 30 дней</h5>
                            <p class="text-muted mb-0 small">
                                Возможность обмена и возврата ДВС и КПП.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-5">
                        <div class="info-icon me-3 fs-2">⚙️</div>
                        <div>
                            <h5 class="fw-bold mb-2">Диагностика</h5>
                            <p class="text-muted mb-0 small">
                                Полная проверка каждого агрегата перед продажей.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
