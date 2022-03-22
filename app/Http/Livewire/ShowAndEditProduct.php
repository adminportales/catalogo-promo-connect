<?php

namespace App\Http\Livewire;

use App\Models\Color;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Site;
use App\Models\Type;
use Livewire\Component;

class ShowAndEditProduct extends Component
{
    protected $listeners = ['showProductListener'];
    public $product;
    public function __construct()
    {
    }
    public function render()
    {
        $colors = Color::all();
        $providers = Provider::all();
        $types = Type::all();
        $sites = Site::all();
        return view('livewire.products.show-and-edit-product', ['product' => $this->product, 'types' => $types, 'providers' => $providers, 'colors' => $colors, 'sites' => $sites]);
    }

    public function showProductListener(Product $product)
    {
        $this->product = $product;
    }

    public function showList()
    {
        $this->product = null;
        $this->emit('showListListener');
    }

    public function updateSites($site_id)
    {
        $site = Site::find($site_id);
        $this->product->sitesProducts()->toggle($site);
        session()->flash('updateSites', 'Actualizacion correcta.');
    }
}
