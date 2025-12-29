@include('layouts.header')
<x-admin-bar />
<main>
    <livewire:engine-cost-modal />
    @yield('content')
</main>
@include('layouts.footer')
