@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="container-lg py-5">
        <h1 class="fs-2 fw-bold mb-5">О компании</h1>

        <div class="row g-5 mb-5 align-items-center">
            <div class="col-lg-6">
                <h2 class="fs-4 fw-bold mb-4">История</h2>
                <p class="lead text-dark mb-4">
                    Компания начала свою работу в 2012 году. За более чем 10 лет работы мы завоевали доверие тысяч клиентов по всей России.
                </p>
                <p class="lead text-dark mb-4">
                    Мы осуществляем прямые поставки контрактных БУ автозапчастей из Японии. Наша специализация — качественные двигатели и комплектующие для легковых и грузовых автомобилей.
                </p>
                <p class="lead text-dark">
                    Отправка в регионы транспортными компаниями. Каждая запчасть проходит тщательный контроль качества перед отправкой.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="public/warehouse-car-parts-storage.jpg" alt="Company warehouse" class="img-fluid rounded shadow">
            </div>
        </div>

        <!-- Why Choose Us -->
        <section class="mb-5">
            <h2 class="fs-3 fw-bold mb-5">Почему клиенты выбирают нас</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <div class="col">
                    <div class="card border border-warning border-2 h-100">
                        <div class="card-body">
                            <div class="fs-2 mb-3">✓</div>
                            <h5 class="card-title fw-bold">Без пробега по РФ</h5>
                            <p class="card-text text-muted">Все запчасти поступают напрямую из Японии без использования на территории России</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-warning border-2 h-100">
                        <div class="card-body">
                            <div class="fs-2 mb-3">📋</div>
                            <h5 class="card-title fw-bold">Полный пакет документов</h5>
                            <p class="card-text text-muted">К каждой запчасти прилагается полная документация и сертификаты качества</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-warning border-2 h-100">
                        <div class="card-body">
                            <div class="fs-2 mb-3">🛡️</div>
                            <h5 class="card-title fw-bold">Расширенная гарантия</h5>
                            <p class="card-text text-muted">Мы предоставляем гарантию на все товары и гарантируем их качество</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-warning border-2 h-100">
                        <div class="card-body">
                            <div class="fs-2 mb-3">💰</div>
                            <h5 class="card-title fw-bold">Низкая цена</h5>
                            <p class="card-text text-muted">Прямые поставки из Японии позволяют нам предложить самые лучшие цены</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-warning border-2 h-100">
                        <div class="card-body">
                            <div class="fs-2 mb-3">⚡</div>
                            <h5 class="card-title fw-bold">Быстрая доставка</h5>
                            <p class="card-text text-muted">Доставка по всей России в кратчайшие сроки с отслеживанием</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border border-warning border-2 h-100">
                        <div class="card-body">
                            <div class="fs-2 mb-3">👨‍💼</div>
                            <h5 class="card-title fw-bold">Профессиональная поддержка</h5>
                            <p class="card-text text-muted">Наши специалисты помогут вам выбрать нужную запчасть</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Block -->
        <div class="alert alert-dark text-white p-5 text-center">
            <h3 class="fs-3 fw-bold mb-4">Хотите узнать больше?</h3>
            <p class="fs-5 mb-4">Свяжитесь с нами и наши специалисты помогут вам найти идеальную запчасть</p>
            <a href="{{ route('contacts') }}" class="btn btn-warning btn-lg fw-bold">Связаться с нами</a>
        </div>
    </div>
@endsection
