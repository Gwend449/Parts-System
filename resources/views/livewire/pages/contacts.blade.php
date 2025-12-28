@extends('layouts.app')
@section('page_title', 'Контакты')
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

                <!-- Alert для сообщений -->
                <div id="formAlert" class="alert alert-dismissible fade" role="alert" style="display: none;">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <div id="formAlertMessage"></div>
                </div>

                <form id="contactPageForm" class="space-y-5">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="contactName" class="form-label fw-semibold">Ваше имя <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg border-2" id="contactName" name="name"
                            placeholder="Иван Иванов" required>
                        <small class="text-danger" style="display: none;" id="nameError"></small>
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="contactPhone" class="form-label fw-semibold">Телефон <span
                                class="text-danger">*</span></label>
                        <input type="tel" class="form-control form-control-lg border-2" id="contactPhone" name="phone"
                            placeholder="+7 (9XX) XXX-XX-XX" required>
                        <small class="text-danger" style="display: none;" id="phoneError"></small>
                    </div>

                    <!-- Car Model -->
                    <div class="mb-4">
                        <label for="contactCar" class="form-label fw-semibold">Марка автомобиля</label>
                        <select class="form-select" id="contactBrand" name="brand">
                            <option value="">Выберите марку...</option>
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
                        <small class="text-danger" style="display: none;" id="messageError"></small>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold py-2" id="contactSubmitBtn">
                        Отправить сообщение
                    </button>
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

    <script>
        document.getElementById('contactPageForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('contactSubmitBtn');
            const alert = document.getElementById('formAlert');
            const alertMessage = document.getElementById('formAlertMessage');

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
                phone: formData.get('phone'),
                brand: formData.get('brand'),
                message: formData.get('message')
            };

            // Отправляем на сервер
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Отправка...';

            try {
                const response = await fetch('{{ route("api.contact-form") }}', {
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
            } catch (error) 
                console.error('Ошибка:', error);
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alertMessage.textContent = 'Произошла ошибка при отправке. Попробуйте позже.';
                alert.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Отправить сообщение';
            }
        });
    </script>
@endsection