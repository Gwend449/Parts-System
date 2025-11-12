<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attribute;
use Illuminate\Support\Facades\Auth;

// #[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
