<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorFinder - Car Engine Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />

    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header id="header" class="sticky-top navbar-dark navbar-expand-md">
        <nav class="navbar navbar-expand-md bg-dark border-bottom border-secondary">
            <div class="container-lg">
                <a class="navbar-brand fw-black fs-5 text-white" href="#home">
                    MOTOR<span class="text-danger">FINDER</span>
                </a>
                <button class="navbar-toggler text-warning" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto gap-3">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#home">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#catalog">Каталог</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#about">О нас</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#contacts">Контакты</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section id="home" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container-lg h-100">
            <div class="row h-100 align-items-center">
                <div class="col-md-6 position-relative z-2">
                    <h1 class="display-4 fw-black text-white mb-4">
                        Подберите<br>
                        <span class="text-danger">двигатель</span><br>
                        под свой<br>
                        автомобиль
                    </h1>
                    <p class="fs-5 fw-bold text-light mb-3">КАЧЕСТВО. ПРОВЕРКА. ГАРАНТИЯ.</p>
                    <p class="text-secondary mb-4 fs-6">
                        Мы подбираем контрактные двигатели под конкретные марки автомобилей. Каждый мотор проходит
                        проверку и поставляется с гарантией.
                    </p>

                    <div class="mb-4">
                        <label for="brandSelect" class="form-label text-secondary fs-6">Выберите марку
                            автомобиля</label>
                        <select id="brandSelect" class="form-select bg-dark text-white border-secondary">
                            <option>Выберите марку</option>
                            <option>Toyota</option>
                            <option>Honda</option>
                            <option>BMW</option>
                            <option>Audi</option>
                            <option>Mercedes</option>
                            <option>Volkswagen</option>
                            <option>Nissan</option>
                            <option>Mazda</option>
                            <option>Ford</option>
                            <option>Hyundai</option>
                            <option>Kia</option>
                        </select>
                    </div>

                    <button class="btn btn-danger btn-lg fw-bold glow-red">
                        🔍 Найти двигатель
                    </button>
                </div>

                <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
                    <div class="position-relative engine-showcase">
                        <div class="engine-circle engine-circle-1">
                            <img src="./public/images/woman.jpg" alt="Engine 1" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="engine-circle engine-circle-2">
                            <img src="/placeholder.svg?height=192&width=192" alt="Engine 2"
                                class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="catalog" class="py-5 bg-light">
        <div class="container-lg">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-black text-dark mb-3">
                    Недавно <span class="text-danger">добавленные</span>
                </h2>
                <p class="text-secondary fs-5">двигатели</p>
                <div class="mx-auto mt-3"
                    style="width: 100px; height: 2px; background: linear-gradient(to right, transparent, var(--bs-danger), transparent);">
                </div>
            </div>

            <div id="enginesContainer" class="row g-4">
                <!-- js -->
            </div>
        </div>
    </section>

    <section id="about" class="py-5 bg-dark text-white">
        <div class="container-lg">
            <div class="row g-5 align-items-center">
                <div class="col-md-6">
                    <div class="position-relative">
                        <img src="/placeholder.svg?height=400&width=400" alt="Компания"
                            class="img-fluid border border-secondary rounded">
                        <div class="position-absolute bottom-0 end-0 border-2 border-danger opacity-50"
                            style="width: 120px; height: 120px; transform: translate(30px, 30px);"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <p class="text-danger fw-bold text-uppercase fs-6 mb-3">О компании</p>
                    <h2 class="display-5 fw-black mb-4">
                        МОЩЬ,<br>
                        НАДЕЖНОСТЬ,<br>
                        <span class="text-danger">УВЕРЕННОСТЬ</span>
                    </h2>
                    <div class="mb-4" style="width: 60px; height: 3px; background-color: var(--bs-danger);"></div>

                    <p class="fs-5 mb-4">
                        Мы подбираем контрактные двигатели под конкретные марки автомобилей с высочайшей точностью и
                        ответственностью.
                    </p>

                    <div class="mb-4">
                        <div class="d-flex gap-3 mb-3">
                            <div class="bg-danger" style="width: 3px; height: auto; min-height: 80px;"></div>
                            <div>
                                <h5 class="fw-bold text-white">Проверка качества</h5>
                                <p class="text-secondary small">Каждый мотор проходит многоэтапную проверку перед
                                    поставкой</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mb-3">
                            <div class="bg-danger" style="width: 3px; height: auto; min-height: 80px;"></div>
                            <div>
                                <h5 class="fw-bold text-white">Полная гарантия</h5>
                                <p class="text-secondary small">Поставляется с полным пакетом документов и гарантией</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="bg-danger" style="width: 3px; height: auto; min-height: 80px;"></div>
                            <div>
                                <h5 class="fw-bold text-white">Надежность инженерии</h5>
                                <p class="text-secondary small">Ценим точность, качество и надежность в каждом деле</p>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-danger btn-lg fw-bold">
                        Связаться с нами
                    </button>
                </div>
            </div>
        </div>
    </section>

    <footer id="contacts" class="bg-black border-top border-secondary text-light py-5">
        <div class="container-lg">
            <div class="row g-5 mb-5">
                <div class="col-md-3">
                    <h5 class="fw-black fs-5 mb-3">
                        MOTOR<span class="text-danger">FINDER</span>
                    </h5>
                    <p class="text-secondary small">
                        Поставка контрактных двигателей из Японии с полной гарантией и документацией.
                    </p>
                </div>

                <div class="col-md-3">
                    <h6 class="fw-bold text-uppercase fs-6 mb-3">Навигация</h6>
                    <ul class="list-unstyled gap-2 d-flex flex-column">
                        <li><a href="#home" class="text-secondary text-decoration-none small">Главная</a></li>
                        <li><a href="#catalog" class="text-secondary text-decoration-none small">Каталог</a></li>
                        <li><a href="#about" class="text-secondary text-decoration-none small">О нас</a></li>
                        <li><a href="#contacts" class="text-secondary text-decoration-none small">Контакты</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6 class="fw-bold text-uppercase fs-6 mb-3">Контакты</h6>
                    <ul class="list-unstyled gap-2 d-flex flex-column text-secondary small">
                        <li>
                            <div class="d-flex gap-2">
                                <span class="text-danger">📞</span>
                                <div>
                                    <p class="mb-0">+7 (924) 735-47-84</p>
                                    <p class="mb-0">+7 (914) 703-66-12</p>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex gap-2">
                            <span class="text-danger">✉</span>
                            <span>info@motorfinder.ru</span>
                        </li>
                        <li class="d-flex gap-2">
                            <span class="text-danger">📍</span>
                            <span>Владивосток, Россия</span>
                        </li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6 class="fw-bold text-uppercase fs-6 mb-3">Мы в сетях</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-2">f</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-2">G</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-2">in</a>
                    </div>
                </div>
            </div>

            <div
                class="border-top border-secondary pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-secondary small mb-3 mb-md-0">© 2025 MotorFinder. Все права защищены.</p>
                <div class="d-flex gap-4">
                    <a href="#" class="text-secondary text-decoration-none small">Политика конфиденциальности</a>
                    <a href="#" class="text-secondary text-decoration-none small">Условия использования</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js">
    </script>
    <script src="script.js"></script>
</body>

</html>
