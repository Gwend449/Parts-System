@if(backpack_auth()->check())
<div class="admin-topbar-wrapper">
    <div class="admin-topbar">
        <a href="{{ backpack_url() }}" class="admin-topbar-btn">Админка</a>
        <a href="{{ backpack_url('engines/') }}" class="admin-topbar-btn admin-topbar-btn-primary">Добавить мотор</a>
    </div>
</div>
@endif
