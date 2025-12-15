@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="container-lg py-5">
        <h1 class="fs-2 fw-bold mb-5">Оплата и доставка</h1>

        <div class="row g-5 mb-5">
            <!-- Payment Section -->
            <div class="col-md-6">
                <h2 class="fs-4 fw-bold mb-4 pb-3 border-bottom border-warning border-3">Условия оплаты</h2>
                <ul class="list-unstyled space-y-4">
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">✓</span>
                        <div>
                            <p class="fw-bold mb-1">Наличные</p>
                            <p class="text-muted">При встрече в нашем офисе</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">✓</span>
                        <div>
                            <p class="fw-bold mb-1">Банковский перевод</p>
                            <p class="text-muted">На расчётный счёт компании</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">✓</span>
                        <div>
                            <p class="fw-bold mb-1">Выставление счёта</p>
                            <p class="text-muted">Для физ. и юр. лиц</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">✓</span>
                        <div>
                            <p class="fw-bold mb-1">Безопасность</p>
                            <p class="text-muted">Все платежи защищены договором</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Delivery Section -->
            <div class="col-md-6">
                <h2 class="fs-4 fw-bold mb-4 pb-3 border-bottom border-warning border-3">Условия доставки</h2>
                <ul class="list-unstyled space-y-4">
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">📦</span>
                        <div>
                            <p class="fw-bold mb-1">Доставка по всей РФ</p>
                            <p class="text-muted">Транспортными компаниями или почтой</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">⏱️</span>
                        <div>
                            <p class="fw-bold mb-1">Сроки доставки</p>
                            <p class="text-muted">От 3 до 7 дней в зависимости от региона</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">🛡️</span>
                        <div>
                            <p class="fw-bold mb-1">Гарантия</p>
                            <p class="text-muted">На все товары распространяется гарантия качества</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <span class="text-warning fw-bold me-3">📍</span>
                        <div>
                            <p class="fw-bold mb-1">Способы получения</p>
                            <p class="text-muted">Самовывоз, почта, курьер или ТК</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contact Block -->
        <div class="alert alert-light border border-warning border-3 p-5 mb-5">
            <h3 class="fs-4 fw-bold mb-4">Возникли вопросы?</h3>
            <p class="mb-4">Свяжитесь с нами и мы ответим на все вопросы о доставке и оплате</p>

            <div class="row g-1 w-10">
                <div class="col">
                    <p class="fw-bold mb-3">Телефоны:</p>
                    <p class="fs-5 mb-2">+7 (910) 121-98-98</p>
                    <p class="fs-5">+7 (910) 121-61-31</p>
                </div>
                <div class="col mb-1">
                    <h5 class="text-muted small fw-bold text-uppercase mb-2">Email</h5>
                    <p class="fs-5 fw-bold">fasti.s02@mail.ru</p>
                </div>
            </div>

        </div>
    </div>
@endsection
