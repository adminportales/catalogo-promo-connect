<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Price;

class Prices extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $product_id, $price, $escala;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.prices.view', [
            'prices' => Price::latest()
						->orWhere('product_id', 'LIKE', $keyWord)
						->orWhere('price', 'LIKE', $keyWord)
						->orWhere('escala', 'LIKE', $keyWord)
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
		$this->price = null;
		$this->escala = null;
    }

    public function store()
    {
        $this->validate([
        ]);

        Price::create([ 
			'product_id' => $this-> product_id,
			'price' => $this-> price,
			'escala' => $this-> escala
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Price Successfully created.');
    }

    public function edit($id)
    {
        $record = Price::findOrFail($id);

        $this->selected_id = $id; 
		$this->product_id = $record-> product_id;
		$this->price = $record-> price;
		$this->escala = $record-> escala;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
        ]);

        if ($this->selected_id) {
			$record = Price::find($this->selected_id);
            $record->update([ 
			'product_id' => $this-> product_id,
			'price' => $this-> price,
			'escala' => $this-> escala
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Price Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Price::where('id', $id);
            $record->delete();
        }
    }
}
