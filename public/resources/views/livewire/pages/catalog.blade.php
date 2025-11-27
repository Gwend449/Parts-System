@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-light py-3 border-bottom border-warning border-3">
        <div class="container-lg">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-warning text-decoration-none">Главная</a></li>
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
            <div class="col-lg-4">
                <div class="card border-2 sticky-lg-top" style="top: 80px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-2">Заинтересовали?</h5>
                        <p class="text-muted small mb-4">Заполните форму и мы свяжемся с вами в течение дня</p>

                        <form id="contactForm" class="space-y-4">
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Ваше имя</label>
                                <input type="text" class="form-control form-control-lg border-2" id="name" name="name" placeholder="Иван Иванов" required>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">Телефон</label>
                                <input type="tel" class="form-control form-control-lg border-2" id="phone" name="phone" placeholder="+7 (9XX) XXX-XX-XX" required>
                            </div>

                            <!-- Car Model -->
                            <div class="mb-3">
                                <label for="carModel" class="form-label fw-semibold">Модель авто</label>
                                <select class="form-select form-select-lg border-2" id="carModel" name="carModel" required>
                                    <option value="">Выберите марку...</option>
                                    <option value="toyota">Toyota</option>
                                    <option value="honda">Honda</option>
                                    <option value="bmw">BMW</option>
                                    <option value="audi">Audi</option>
                                    <option value="ford">Ford</option>
                                    <option value="nissan">Nissan</option>
                                    <option value="mazda">Mazda</option>
                                    <option value="volkswagen">Volkswagen</option>
                                </select>
                            </div>

                            <!-- Message -->
                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">Комментарий (опционально)</label>
                                <textarea class="form-control border-2" id="message" name="message" rows="3" placeholder="Добавьте дополнительную информацию..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-warning w-100 fw-bold py-2">Отправить запрос</button>

                            <!-- Privacy Text -->
                            <p class="text-muted small text-center pt-3 border-top">
                                Мы не передаём ваши данные третьим лицам. Гарантируем конфиденциальность.
                            </p>
                        </form>

                        <!-- Contact Info -->
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-muted small fw-semibold mb-3">Или свяжитесь с нами напрямую:</p>
                            <p class="fw-bold mb-2">+7 (924) 735-47-84</p>
                            <p class="fw-bold mb-2">+7 (914) 703-66-12</p>
                            <p class="fw-bold">+7 (908) 993-66-12</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
