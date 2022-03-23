<?php

namespace App\Http\Livewire;

use App\Models\GlobalAttribute;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class Products extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $internal_sku, $sku_parent, $sku, $name, $price, $description, $stock, $type_id, $color_id, $provider_id;
    public $updateMode = false;
    public $showProduct = "d-none";
    public $showList = "";

    protected $listeners = ['showListListener'];


    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';
        $utilidad = GlobalAttribute::find(1);
        $products = Product::orWhere('internal_sku', 'LIKE', $keyWord)
            ->orWhere('sku_parent', 'LIKE', $keyWord)
            ->orWhere('sku', 'LIKE', $keyWord)
            ->orWhere('name', 'LIKE', $keyWord)
            ->orWhere('price', 'LIKE', $keyWord)
            ->orWhere('description', 'LIKE', $keyWord)
            ->orWhere('stock', 'LIKE', $keyWord)
            ->orWhere('type_id', 'LIKE', $keyWord)
            ->orWhere('color_id', 'LIKE', $keyWord)
            ->orWhere('provider_id', 'LIKE', $keyWord)
            ->paginate(10);
        return view('livewire.products.view', [
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
        $this->internal_sku = null;
        $this->sku_parent = null;
        $this->sku = null;
        $this->name = null;
        $this->price = null;
        $this->description = null;
        $this->stock = null;
        $this->type_id = null;
        $this->color_id = null;
        $this->provider_id = null;
    }

    public function store()
    {
        $this->validate([
            'internal_sku' => 'required',
            'sku' => 'required',
        ]);

        Product::create([
            'internal_sku' => $this->internal_sku,
            'sku_parent' => $this->sku_parent,
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'stock' => $this->stock,
            'type_id' => $this->type_id,
            'color_id' => $this->color_id,
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
        $this->internal_sku = $record->internal_sku;
        $this->sku_parent = $record->sku_parent;
        $this->sku = $record->sku;
        $this->name = $record->name;
        $this->price = $record->price;
        $this->description = $record->description;
        $this->stock = $record->stock;
        $this->type_id = $record->type_id;
        $this->color_id = $record->color_id;
        $this->provider_id = $record->provider_id;

        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'internal_sku' => 'required',
            'sku' => 'required',
        ]);

        if ($this->selected_id) {
            $record = Product::find($this->selected_id);
            $record->update([
                'internal_sku' => $this->internal_sku,
                'sku_parent' => $this->sku_parent,
                'sku' => $this->sku,
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'stock' => $this->stock,
                'type_id' => $this->type_id,
                'color_id' => $this->color_id,
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
    public function showProduct(Product $product)
    {
        $this->showList = 'd-none';
        $this->showProduct = '';
        $this->emit('showProductListener', $product->id);
    }

    public function showListListener()
    {
        $this->showProduct = 'd-none';
        $this->showList = '';
    }
}
