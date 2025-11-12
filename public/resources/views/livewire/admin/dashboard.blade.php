@extends('layouts.admin')

@section('content')
<div>
    <h1>Добро пожаловать в админку!</h1>
    <p>Это главная страница админ-панели</p>

    <!-- Ваш контент дашборда -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Статистика</h5>
                    <p class="card-text">Количество товаров: 150</p>
                </div>
            </div>
        </div>
        <!-- Другие карточки... -->
    </div>
</div>
@endsection
