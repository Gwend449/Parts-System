    @livewireScripts

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container-lg">
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                <div>
                    <h5 class="text-warning fw-bold mb-3">Амур Авто</h5>
                    <p class="text-light">Контрактные запчасти из Японии на легковые и грузовые авто</p>
                </div>
                <div>
                    <h6 class="fw-bold mb-3">Меню</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('delivery') }}"
                                class="text-light text-decoration-none hover-text-warning">Оплата и доставка</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}"
                                class="text-light text-decoration-none hover-text-warning">О нас</a></li>
                        <li><a href="{{ route('contacts') }}"
                                class="text-light text-decoration-none hover-text-warning">Контакты</a></li>
                    </ul>
                </div>
                <div>
                    <h6 class="fw-bold mb-3">Контакты</h6>
                    <p class="text-light mb-2">+7 (924) 735-47-84</p>
                    <p class="text-light mb-2">+7 (914) 703-66-12</p>
                    <p class="text-light">+7 (908) 993-66-12</p>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center text-light">
                <p>&copy; 2025 Амур Авто. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
