<?php

namespace App\Http\Controllers;
use App\Models\Engine;

class EngineController extends Controller
{
    public function show($slug)
    {
        $engine = Engine::where('slug', $slug)->firstOrFail();

        return view('livewire.pages.engine-show', compact('engine'));
    }

}
