<?php

namespace App\Livewire\Catalog;

use Livewire\Component;

class BrandSelector extends Component
{

    public $brands = [
        "ALFA ROMEO",
        "AUDI",
        "BMW",
        "FORD",
        "HONDA",
        "HYUNDAI",
        "ISUZU",
        "JAGUAR",
        "JEEP",
        "KIA",
        "MAZDA","MERCEDES","MAN","MINI","MITSUBISHI","NISSAN","OPEL","PEUGEOT","PORSCHE","RENAULT","SUBARU","SUZUKI","TOYOTA","VOLKSWAGEN","VOLVO",
    ];

    public $selectedBrand = null;

    public function selectBrand($brand)
    {
        $this->selectedBrand = $brand;
    }

    public function goToCatalog()
    {
        if ($this->selectedBrand) {
            return redirect()->to('/catalog?brand=' . urlencode($this->selectedBrand));
        }

        return redirect()->to('/catalog');
    }

    public function render()
    {
        return view('livewire.catalog.brand-selector');
    }
}
