<?php

namespace App\Http\Livewire;

use App\Models\GlobalAttribute;
use App\Models\Site;
use Livewire\Component;

class ProductsBySite extends Component
{
    public $site;

    protected $listeners = ['showProductsBySite'];

    public function showProductsBySite(Site $site)
    {
        $this->site = $site;
    }

    public function render()
    {
        $products = [];
        if ($this->site) {
            $products = $this->site->sitesProducts;
        }
        return view('livewire.sites.products-by-site', ['products' => $products]);
    }

    public function clear()
    {
        $this->product = null;
    }
}
