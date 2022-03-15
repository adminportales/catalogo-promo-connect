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
        return view('cotizador.product', ['product' => $this->product, 'utilidad' => $utilidad]);
    }
    public function clear()
    {
        $this->product = null;
    }
}
