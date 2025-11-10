@extends('livewire.layout.admin.admin')
@section('content')
    <div class="page">
        <!-- ======== SIDEBAR ======== -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('admin.dashboard') }}" class="text-white">Admin</a>
                </h1>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-dashboard"></i>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.brands') }}">
                                    <span class="nav-link-title">Марки</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.models') }}">
                                    <span class="nav-link-title">Модели</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.engines') }}">
                                    <span class="nav-link-title">Двигатели</span>
                                </a>
                            </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- ======== MAIN CONTENT ======== -->
        <div class="page-wrapper">
            <header class="navbar navbar-expand-md navbar-light d-print-none">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <h2 class="page-title">@yield('title', 'Dashboard')</h2>
                    <div class="navbar-nav flex-row order-md-last">
                        <div class="nav-item">
                            <button wire:click="logout" class="btn btn-danger btn-sm">Выйти</button>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page-body">
                <div class="container-fluid py-3">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
@endsection
