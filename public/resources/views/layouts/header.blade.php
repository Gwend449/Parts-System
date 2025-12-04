<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1749 АвтоРазбор</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/styles.css'])
</head>
@livewireStyles

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark sticky-top shadow">
        <div class="container-lg">
            <a class="navbar-brand text-brand" href="{{ route('home') }}">
                <span class="fw-bold fs-5">1749</span> <span class="fw-bold fs-5 text-light">АвтоРазбор</span>
            </a>
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button> -->
            <div class="navbar-nav" id="navbarNav">
                <ul class="navbar-nav d-flex flex-row gap-3 mb-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('catalog') }}">Каталог</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('delivery') }}">Доставка</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">О нас</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacts') }}">Контакты</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
