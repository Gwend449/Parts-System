@extends('layouts.app')
@section('page_title', $engine->title)
@section('content')
    <div class="page-container py-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"
                        class="text-brand text-decoration-none">Главная</a></li>
                <li class="breadcrumb-item"><a href="{{ route('catalog') }}"
                        class="text-brand text-decoration-none">Каталог</a></li>
                <li class="breadcrumb-item active">{{ $engine->title }}</li>
            </ol>
        </nav>

        <livewire:engine-show-page :engine="$engine" />
    </div>
@endsection