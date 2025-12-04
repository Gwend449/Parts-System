@livewireScripts

<!-- Footer -->
<footer class="bg-dark text-white py-5">
    <div class="container-lg">
        <div class="container-lg">
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4 align-items-start">
                <!-- Первая колонка - лого и описание -->
                <div class="col">
                    <div
                        class="d-flex flex-column flex-md-row align-items-center align-items-md-start text-center text-md-start">
                        <div class="block me-md-3 mb-3 mb-md-0 mt-1">
                            <img class="img-thumbnail rounded" style="width: 120px; height: 100; object-fit: cover;"
                                src="/images/logo.jpg" alt="">
                        </div>
                        <div class="block">
                            <h5 class="text-brand fw-bold mb-2">1749 <span
                                    class="fw-bold fs-5 text-white">АвтоРазбор</span></h5>
                            <p class="text-light small w-75">Контрактные запчасти на легковые и грузовые авто с гарантией.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Вторая колонка - меню -->
                <div class="col">
                    <h6 class="fw-bold mb-3 text-center text-md-start">Меню</h6>
                    <ul class="list-unstyled text-center text-md-start">
                        <li class="mb-2"><a href="{{ route('delivery') }}"
                                class="text-light text-decoration-none hover-text-warning">Оплата и доставка</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}"
                                class="text-light text-decoration-none hover-text-warning">О нас</a></li>
                        <li><a href="{{ route('contacts') }}"
                                class="text-light text-decoration-none hover-text-warning">Контакты</a></li>
                    </ul>
                </div>

                <!-- Третья колонка - контакты -->
                <div class="col">
                    <h6 class="fw-bold mb-3 text-center text-md-start">Контакты</h6>
                    <div class="text-center text-md-start">
                        <p class="text-light mb-2">+7 (924) 735-47-84</p>
                        <p class="text-light mb-2">+7 (914) 703-66-12</p>
                        <p class="text-light">+7 (908) 993-66-12</p>
                    </div>
                </div>
            </div>

            <hr class="bg-secondary">
            <div class="text-center text-light">
                <p>&copy; 2025 АвтоРазбор <span class="text-brand">1749</span>. Все права защищены.</p>
            </div>
        </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
