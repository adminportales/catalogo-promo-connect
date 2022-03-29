<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductAttribute;

class ProductAttributes extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $product_id, $attribute, $slug, $value;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.productAttributes.view', [
            'productAttributes' => ProductAttribute::latest()
						->orWhere('product_id', 'LIKE', $keyWord)
						->orWhere('attribute', 'LIKE', $keyWord)
						->orWhere('slug', 'LIKE', $keyWord)
						->orWhere('value', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
        $this->updateMode = false;
    }
	
    private function resetInput()
    {		
		$this->product_id = null;
		$this->attribute = null;
		$this->slug = null;
		$this->value = null;
    }

    public function store()
    {
        $this->validate([
		'product_id' => 'required',
		'attribute' => 'required',
		'slug' => 'required',
		'value' => 'required',
        ]);

        ProductAttribute::create([ 
			'product_id' => $this-> product_id,
			'attribute' => $this-> attribute,
			'slug' => $this-> slug,
			'value' => $this-> value
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'ProductAttribute Successfully created.');
    }

    public function edit($id)
    {
        $record = ProductAttribute::findOrFail($id);

        $this->selected_id = $id; 
		$this->product_id = $record-> product_id;
		$this->attribute = $record-> attribute;
		$this->slug = $record-> slug;
		$this->value = $record-> value;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'product_id' => 'required',
		'attribute' => 'required',
		'slug' => 'required',
		'value' => 'required',
        ]);

        if ($this->selected_id) {
			$record = ProductAttribute::find($this->selected_id);
            $record->update([ 
			'product_id' => $this-> product_id,
			'attribute' => $this-> attribute,
			'slug' => $this-> slug,
			'value' => $this-> value
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'ProductAttribute Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = ProductAttribute::where('id', $id);
            $record->delete();
        }
    }
}
