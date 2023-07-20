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
        return view('cotizador.product', ['product' => $this->product]);
    }
    public function clear()
    {
        $this->product = null;
    }
}
