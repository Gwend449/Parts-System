<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Админ-панель | @yield('title', 'Dashboard')</title>

    <!-- Tabler Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js" defer></script>

    @livewireStyles
</head>

<body class="layout-fluid">
    @yield('content')

    @livewireScripts
</body>
</html>
