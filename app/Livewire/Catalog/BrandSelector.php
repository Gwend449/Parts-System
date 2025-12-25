<?php

namespace App\Livewire\Catalog;

use Livewire\Component;

class BrandSelector extends Component
{
    public $brands;
    public $selectedBrand = null;

    public function mount()
    {
        $brandsConfig = config('brands');

        if (!is_array($brandsConfig)) {
            throw new \Exception('Config brands.php not loaded');
        }

        $this->brands = array_keys($brandsConfig);
    }


    public function selectBrand($brand)
    {
        $this->selectedBrand = $brand;
    }

    public function goToCatalog()
    {
        if ($this->selectedBrand) {
            return redirect()->to(
                route('catalog', ['brand' => $this->selectedBrand])
            );
        }

        return redirect()->to(route('catalog'));
    }

    public function render()
    {
        return view('livewire.catalog.brand-selector');
    }
}

