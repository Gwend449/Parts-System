@extends('layouts.app')
@section('page_title', 'Главная')
@section('content')
    <!-- Hero Section -->
    <section class="hero-section bg-dark text-white py-5">
        <div class="container-lg">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-light mb-4">Моторы для любых моделей авто</h1>
                    <p class="lead text-light mb-4">Подберите двигатель под свою марку за 30 секунд</p>
                    <a href="{{ route('catalog') }}" class="btn btn-warning btn-lg fw-bold text-light">Выбрать мотор</a>
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
        <livewire:catalog.recent-engines>
    </section>


    <!-- About Company -->
    <section class="py-5 bg-light">
        <div class="container-lg">
            <div class="row g-5 align-items-start">

                <!-- Left Column -->
                <div class="col-lg-6">

                    <h2 class="fs-2 fw-bold mb-4">О компании</h2>

                    <p class="lead text-dark mb-3">
                        Мы используем только проверенные и надёжные автозапчасти, прошедшие тщательную диагностику.
                        Каждый товар имеет подтверждённую историю, пробег и состояние.
                    </p>

                    <p class="lead text-dark mb-4">
                        Мы предоставляем 30 дней гарантии. Выбирайте АВТОРАЗБОР 1749 — выбирайте надежность.
                    </p>

                    <ul class="list-unstyled space-y-3">

                        <li class="d-flex align-items-start mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#f0ad4e"
                                viewBox="0 0 24 24" class="me-3 flex-shrink-0 mt-1">
                                <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm5
                                                         8.59-6.29 6.3a1 1 0 0 1-1.42 0L7 14.59a1 1 0 1 1
                                                         1.41-1.41l2.29 2.3 5.59-5.59A1 1 0 0 1 17 10.59z" />
                            </svg>
                            <span class="fs-5 fw-medium">Без пробега по РФ</span>
                        </li>

                        <li class="d-flex align-items-start mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#f0ad4e"
                                viewBox="0 0 24 24" class="me-3 flex-shrink-0 mt-1">
                                <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm5
                                                         8.59-6.29 6.3a1 1 0 0 1-1.42 0L7 14.59a1 1 0 1 1
                                                         1.41-1.41l2.29 2.3 5.59-5.59A1 1 0 0 1 17 10.59z" />
                            </svg>
                            <span class="fs-5 fw-medium">Полный пакет документов</span>
                        </li>

                        <li class="d-flex align-items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#f0ad4e"
                                viewBox="0 0 24 24" class="me-3 flex-shrink-0 mt-1">
                                <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm5
                                                         8.59-6.29 6.3a1 1 0 0 1-1.42 0L7 14.59a1 1 0 1 1
                                                         1.41-1.41l2.29 2.3 5.59-5.59A1 1 0 0 1 17 10.59z" />
                            </svg>
                            <span class="fs-5 fw-medium">Расширенная гарантия</span>
                        </li>

                    </ul>

                </div>

                <!-- Right Column -->
                <div class="col-lg-6">
                    <img src="/images/logo.jpg" alt="Company" class="img-fluid rounded-3 shadow-lg">
                </div>

            </div>
        </div>
    </section>
@endsection
