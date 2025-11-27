@extends('layouts.app')

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

    <div class="row g-5">
        <!-- Левая колонка с изображением -->
        <div class="col-lg-6">
            <img
                src="{{ $engine->image ?? '/images/placeholder-engine.png' }}"
                class="img-fluid rounded border"
                alt="{{ $engine->title }}"
            >
        </div>

        <!-- Правая колонка -->
        <div class="col-lg-6">
            <h1 class="fw-bold">{{ $engine->title }}</h1>

            <p class="text-muted mb-1">Марка: <strong>{{ $engine->brand }}</strong></p>
            <p class="text-muted mb-3">OEM: <strong>{{ $engine->oem }}</strong></p>

            <h3 class="fw-bold mb-4">
                {{ number_format($engine->price, 0, ',', ' ') }} ₽
            </h3>

            <!-- Кнопка узнать стоимость -->
            <button
                class="btn btn-warning btn-lg w-100 mb-4"
                onclick="Livewire.emit('openCostModal', {{ $engine->id }})"
            >
                Узнать стоимость
            </button>

            <!-- Блок описания -->
            <h4 class="fw-semibold mt-4">Описание</h4>
            <p class="text-muted">
                {{ $engine->description ?: 'Описание будет добавлено позже.' }}
            </p>

            <!-- Совместимость -->
            <h5 class="fw-semibold mt-4">Совместимость</h5>
            <p class="text-muted">
                {{ $engine->fit_for ?: 'Информация уточняется.' }}
            </p>
        </div>
    </div>

</div>
@endsection
