<?php

namespace App\Http\Livewire;

use App\Models\GlobalAttribute;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class Catalogo extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $sku, $name, $price, $description, $stock, $type, $color, $image, $ecommerce, $offer, $discount, $provider_id;
    public $updateMode = false;

    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';

        $utilidad = GlobalAttribute::find(1);

        $products = Product::latest()
            ->orWhere('sku', 'LIKE', $keyWord)
            ->orWhere('name', 'LIKE', $keyWord)
            ->orWhere('price', 'LIKE', $keyWord)
            ->orWhere('description', 'LIKE', $keyWord)
            ->orWhere('stock', 'LIKE', $keyWord)
            ->orWhere('type', 'LIKE', $keyWord)
            ->orWhere('color', 'LIKE', $keyWord)
            ->orWhere('image', 'LIKE', $keyWord)
            ->orWhere('ecommerce', 'LIKE', $keyWord)
            ->orWhere('offer', 'LIKE', $keyWord)
            ->orWhere('discount', 'LIKE', $keyWord)
            ->orWhere('provider_id', 'LIKE', $keyWord)
            ->paginate(25);
        // dd($products);
        return view('cotizador.catalogo.view', [
            'products' => $products, 'utilidad' => $utilidad
        ]);
    }

    public function cancel()
    {
        $this->resetInput();
        $this->updateMode = false;
    }

    private function resetInput()
    {
        $this->sku = null;
        $this->name = null;
        $this->price = null;
        $this->description = null;
        $this->stock = null;
        $this->type = null;
        $this->color = null;
        $this->image = null;
        $this->ecommerce = null;
        $this->offer = null;
        $this->discount = null;
        $this->provider_id = null;
    }

    public function store()
    {
        $this->validate([
            'sku' => 'required',
        ]);

        Product::create([
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'stock' => $this->stock,
            'type' => $this->type,
            'color' => $this->color,
            'image' => $this->image,
            'ecommerce' => $this->ecommerce,
            'offer' => $this->offer,
            'discount' => $this->discount,
            'provider_id' => $this->provider_id
        ]);

        $this->resetInput();
        $this->emit('closeModal');
        session()->flash('message', 'Product Successfully created.');
    }

    public function edit($id)
    {
        $record = Product::findOrFail($id);

        $this->selected_id = $id;
        $this->sku = $record->sku;
        $this->name = $record->name;
        $this->price = $record->price;
        $this->description = $record->description;
        $this->stock = $record->stock;
        $this->type = $record->type;
        $this->color = $record->color;
        $this->image = $record->image;
        $this->ecommerce = $record->ecommerce;
        $this->offer = $record->offer;
        $this->discount = $record->discount;
        $this->provider_id = $record->provider_id;

        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'sku' => 'required',
        ]);

        if ($this->selected_id) {
            $record = Product::find($this->selected_id);
            $record->update([
                'sku' => $this->sku,
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'stock' => $this->stock,
                'type' => $this->type,
                'color' => $this->color,
                'image' => $this->image,
                'ecommerce' => $this->ecommerce,
                'offer' => $this->offer,
                'discount' => $this->discount,
                'provider_id' => $this->provider_id
            ]);

            $this->resetInput();
            $this->updateMode = false;
            session()->flash('message', 'Product Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Product::where('id', $id);
            $record->delete();
        }
    }
}
