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

                    <div id="catalogFormAlert" class="alert alert-dismissible fade" role="alert" style="display: none;">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div id="catalogFormAlertMessage"></div>
                    </div>

                    <form id="catalogForm">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Ваше имя <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 border-1 py-2" name="name"
                                placeholder="Иван Иванов" required>
                            <small class="text-danger" style="display: none;" id="nameError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control rounded-3 border-1 py-2" name="email"
                                placeholder="ivan@example.com" required>
                            <small class="text-danger" style="display: none;" id="emailError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Телефон <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control rounded-3 border-1 py-2" name="phone"
                                placeholder="+7 (9XX) XXX-XX-XX" required>
                            <small class="text-danger" style="display: none;" id="phoneError"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Марка авто</label>
                            <select class="form-select" name="brand">
                                <option value="">Выберите марку...</option>
                                @foreach(\App\Models\Engine::select('brand')->distinct()->pluck('brand') as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Комментарий (опционально)</label>
                            <textarea class="form-control rounded-3 border-2" rows="3" name="message"
                                placeholder="Добавьте дополнительную информацию..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-brand-primary w-100 fw-bold py-2 rounded-3"
                            id="catalogSubmitBtn">
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
    </div>

    <script>
        document.getElementById('catalogForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('catalogSubmitBtn');
            const alert = document.getElementById('catalogFormAlert');
            const alertMessage = document.getElementById('catalogFormAlertMessage');

            // Очищаем предыдущие ошибки
            document.querySelectorAll('small[id$="Error"]').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
            alert.style.display = 'none';

            // Собираем данные формы
            const formData = new FormData(form);
            const data = {
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                brand: formData.get('brand'),
                message: formData.get('message')
            };

            // Отправляем на сервер
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Отправка...';

            try {
                const response = await fetch('{{ route("api.catalog-form") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    // Успешно отправлено
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alertMessage.textContent = result.message;
                    alert.style.display = 'block';

                    // Очищаем форму
                    form.reset();
                } else {
                    // Ошибка валидации
                    alert.className = 'alert alert-danger alert-dismissible fade show';

                    if (result.errors) {
                        // Выводим ошибки валидации
                        Object.keys(result.errors).forEach(field => {
                            const errorElement = document.getElementById(field + 'Error');
                            if (errorElement) {
                                errorElement.textContent = result.errors[field][0];
                                errorElement.style.display = 'block';
                            }
                        });
                        alertMessage.textContent = 'Пожалуйста, исправьте ошибки в форме';
                    } else {
                        alertMessage.textContent = result.message || 'Произошла ошибка при отправке формы';
                    }
                    alert.style.display = 'block';
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alertMessage.textContent = 'Произошла ошибка при отправке. Попробуйте позже.';
                alert.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Отправить запрос';
            }
        });
    </script>
@endsection