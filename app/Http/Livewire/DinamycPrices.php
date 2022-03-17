<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DinamycPrice;

class DinamycPrices extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $type, $provider_change, $type_change, $amount, $product_id, $site_id;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.dinamycPrices.view', [
            'dinamycPrices' => DinamycPrice::latest()
						->orWhere('type', 'LIKE', $keyWord)
						->orWhere('provider_change', 'LIKE', $keyWord)
						->orWhere('type_change', 'LIKE', $keyWord)
						->orWhere('amount', 'LIKE', $keyWord)
						->orWhere('product_id', 'LIKE', $keyWord)
						->orWhere('site_id', 'LIKE', $keyWord)
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
		$this->type = null;
		$this->provider_change = null;
		$this->type_change = null;
		$this->amount = null;
		$this->product_id = null;
		$this->site_id = null;
    }

    public function store()
    {
        $this->validate([
		'type' => 'required',
		'provider_change' => 'required',
		'type_change' => 'required',
		'amount' => 'required',
		'product_id' => 'required',
        ]);

        DinamycPrice::create([ 
			'type' => $this-> type,
			'provider_change' => $this-> provider_change,
			'type_change' => $this-> type_change,
			'amount' => $this-> amount,
			'product_id' => $this-> product_id,
			'site_id' => $this-> site_id
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'DinamycPrice Successfully created.');
    }

    public function edit($id)
    {
        $record = DinamycPrice::findOrFail($id);

        $this->selected_id = $id; 
		$this->type = $record-> type;
		$this->provider_change = $record-> provider_change;
		$this->type_change = $record-> type_change;
		$this->amount = $record-> amount;
		$this->product_id = $record-> product_id;
		$this->site_id = $record-> site_id;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'type' => 'required',
		'provider_change' => 'required',
		'type_change' => 'required',
		'amount' => 'required',
		'product_id' => 'required',
        ]);

        if ($this->selected_id) {
			$record = DinamycPrice::find($this->selected_id);
            $record->update([ 
			'type' => $this-> type,
			'provider_change' => $this-> provider_change,
			'type_change' => $this-> type_change,
			'amount' => $this-> amount,
			'product_id' => $this-> product_id,
			'site_id' => $this-> site_id
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'DinamycPrice Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = DinamycPrice::where('id', $id);
            $record->delete();
        }
    }
}
