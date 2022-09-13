<?php

namespace App\Http\Livewire;

use App\Models\Color;
use App\Models\Medium;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Site;
use App\Models\Type;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowAndEditProduct extends Component
{
    use WithFileUploads;

    public $product, $urlImage, $fileImage, $images;

    public function __construct()
    {
    }
    public function render()
    {
        $colors = Color::all();
        $providers = Provider::all();
        $types = Type::all();
        $sites = Site::all();
        $this->images = $this->product->images;
        return view('livewire.products.show-and-edit-product', ['types' => $types, 'providers' => $providers, 'colors' => $colors, 'sites' => $sites]);
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

    public function saveImage()
    {
        if (!(strlen($this->urlImage) > 0 || $this->fileImage)) {
            return;
        }
        if ($this->fileImage) {
            $this->validate([
                'fileImage' => 'image|max:8048', // 2MB Max
            ]);

            $filePath = time() . $this->fileImage->getClientOriginalName();
            $this->fileImage->storeAs('public/photos', $filePath);
            $medium  = Medium::create([
                'name' => $filePath,
                'path' => '/storage/photos/' . $filePath
            ]);
            $this->photos = null;
            $this->product->images()->create(['image_url' => $medium->path]);
        }
        if (strlen($this->urlImage) > 0) {
            $this->product->images()->create(['image_url' => $this->urlImage]);
            $this->urlImage = '';
        }
        $this->images = $this->product->images;
    }
}
