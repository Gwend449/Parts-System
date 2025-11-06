<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Админ-панель')</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
  </head>
  <body>
    <div class="page">
      @include('admin.partials.header')
      <div class="page-wrapper">
        <div class="page-body">
          <div class="container-xl">
            @yield('content')
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
  </body>
</html>
