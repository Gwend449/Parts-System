@extends('layouts.app')
@section('page_title', 'Каталог')
@section('content')
    <!-- Breadcrumb -->
    <div class="bg-light py-3 border-bottom border-warning border-3">
        <div class="container-lg">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-brand text-decoration-none">Главная</a></li>
                    <li class="breadcrumb-item active">Каталог моторов</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-lg py-5">
        <h1 class="fs-2 fw-bold mb-3">Каталог моторов</h1>
        <p class="text-muted lead mb-5">Выберите интересующий вас мотор и заполните форму для уточнения деталей</p>

        <div class="row g-4">
            <!-- Motors List -->
            <div class="col-lg-8">
                <div id="motorsList" class="space-y-4">
                    <livewire:catalog.engines-catalog />
                </div>
            </div>

            <!-- User Form Sidebar -->
            <div class="col-lg-4 border-start-lg ps-lg-4">
                <div class="sticky-lg-top card-body p-4" style="top: 80px">
                    <h5 class="card-title fw-bold mb-2">Заинтересовали?</h5>
                    <p class="text-muted small mb-4">
                        Заполните форму и мы свяжемся с вами в течение дня
                    </p>

                    <form id="contactForm">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Ваше имя</label>
                            <input type="text" class="form-control rounded-3 border-2 py-2" placeholder="Иван Иванов"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Телефон</label>
                            <input type="tel" class="form-control rounded-3 border-2 py-2" placeholder="+7 (9XX) XXX-XX-XX"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Модель авто</label>
                            <select class="form-select" wire:model="brand">
                                <option value="">Выберите марку...</option>
                                @foreach(\App\Models\Engine::select('brand')->distinct()->pluck('brand') as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Комментарий (опционально)</label>
                            <textarea class="form-control rounded-3 border-2" rows="3"
                                placeholder="Добавьте дополнительную информацию..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-brand-primary w-100 fw-bold py-2 rounded-3">
                            Отправить запрос
                        </button>

                        <p class="text-muted small text-center pt-3 border-top mt-4">
                            Мы не передаём ваши данные третьим лицам. Гарантируем конфиденциальность.
                        </p>

                        <div class="mt-4 pt-3 border-top small">
                            <p class="text-muted fw-semibold mb-2">Связаться напрямую:</p>
                            <p class="fw-bold mb-1">+7 (910) 121-98-98</p>
                            <p class="fw-bold mb-1">+7 (910) 121-61-31</p>
                        </div>
                    </form>
                </div>

            </div>
        </div>
@endsection