@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="container-lg py-5">
        <h1 class="fs-2 fw-bold mb-5">Контакты</h1>

        <div class="row g-5 mb-5">
            <!-- Contact Info -->
            <div class="col-lg-6">
                <h2 class="fs-4 fw-bold mb-4 pb-3 border-bottom border-warning border-3">Информация</h2>

                <div class="mb-5">
                    <h5 class="text-muted small fw-bold text-uppercase mb-2">Адрес</h5>
                    <p class="fs-5 fw-bold">г. Нижний Новгород, проспект Гагарина, д. 69</p>
                </div>

                <div class="mb-5">
                    <h5 class="text-muted small fw-bold text-uppercase mb-2">Телефоны</h5>
                    <p class="fs-5 fw-bold mb-2">+7 (910) 121-98-98</p>
                    <p class="fs-5 fw-bold mb-2">+7 (910) 121-61-31</p>
                </div>

                <div class="mb-5">
                    <h5 class="text-muted small fw-bold text-uppercase mb-2">Email</h5>
                    <p class="fs-5 fw-bold">fasti.s02@mail.ru</p>
                </div>

                <div>
                    <h5 class="text-muted small fw-bold text-uppercase mb-2">Режим работы</h5>
                    <p class="text-dark">Ежедневно: <br> 09:00 - 21:00</p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-6">
                <h2 class="fs-4 fw-bold mb-4 pb-3 border-bottom border-warning border-3">Форма обратной связи</h2>

                <form id="contactPageForm" class="space-y-5">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="contactName" class="form-label fw-semibold">Ваше имя <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg border-2" id="contactName" name="name"
                            placeholder="Иван Иванов" required>
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="contactPhone" class="form-label fw-semibold">Телефон <span
                                class="text-danger">*</span></label>
                        <input type="tel" class="form-control form-control-lg border-2" id="contactPhone" name="phone"
                            placeholder="+7 (9XX) XXX-XX-XX" required>
                    </div>

                    <!-- Car Model -->
                    <div class="mb-4">
                        <label for="contactCar" class="form-label fw-semibold">Марка автомобиля</label>
                        <select class="form-select" wire:model="brand">
                            <option value="">Все бренды</option>
                            @foreach(\App\Models\Engine::select('brand')->distinct()->pluck('brand') as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <label for="contactMessage" class="form-label fw-semibold">Комментарий <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control border-2" id="contactMessage" name="message" rows="5"
                            placeholder="Напишите вашу заявку..." required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold py-2">Отправить сообщение</button>
                </form>
            </div>
        </div>

        <!-- Map -->
        <div class="mb-5">
            <h2 class="fs-4 fw-bold mb-4">Найдите нас на карте</h2>
            <div class="ratio ratio-16x9 rounded overflow-hidden border border-2 border-gray-300">
                <img src="public/map-location.png" alt="Map" class="img-fluid">
            </div>
        </div>
    </div>
@endsection
