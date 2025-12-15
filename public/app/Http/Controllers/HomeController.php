<?php

namespace App\Http\Controllers;
use App\Models\Engine;

class HomeController extends Controller
{
    public function index()
    {
        $latestEngines = Engine::query()
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.pages.home', compact('latestEngines'));
    }
}

