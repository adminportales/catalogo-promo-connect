<?php

namespace App\Http\Livewire;

use App\Models\GlobalAttribute;
use App\Models\Product as ModelsProduct;
use Livewire\Component;

class Product extends Component
{
    public $product;

    protected $listeners = ['showProductListener'];

    public function showProductListener(ModelsProduct $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        $utilidad = GlobalAttribute::find(1);
        $utilidad = (float) $utilidad->value;

        if (auth()->user()->settingsUser) {
            $utilidad = (float)(auth()->user()->settingsUser->utility > 0 ?  auth()->user()->settingsUser->utility :  $utilidad);
        }
        return view('cotizador.product', ['product' => $this->product, 'utilidad' => $utilidad]);
    }
    public function clear()
    {
        $this->product = null;
    }
}
