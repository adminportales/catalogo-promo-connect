<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Provider;

class Providers extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $company, $email, $phone, $contact, $discount;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.providers.view', [
            'providers' => Provider::latest()
						->orWhere('company', 'LIKE', $keyWord)
						->orWhere('email', 'LIKE', $keyWord)
						->orWhere('phone', 'LIKE', $keyWord)
						->orWhere('contact', 'LIKE', $keyWord)
						->orWhere('discount', 'LIKE', $keyWord)
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
		$this->company = null;
		$this->email = null;
		$this->phone = null;
		$this->contact = null;
		$this->discount = null;
    }

    public function store()
    {
        $this->validate([
		'company' => 'required',
		'email' => 'required',
		'phone' => 'required',
		'discount' => 'required',
        ]);

        Provider::create([ 
			'company' => $this-> company,
			'email' => $this-> email,
			'phone' => $this-> phone,
			'contact' => $this-> contact,
			'discount' => $this-> discount
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Provider Successfully created.');
    }

    public function edit($id)
    {
        $record = Provider::findOrFail($id);

        $this->selected_id = $id; 
		$this->company = $record-> company;
		$this->email = $record-> email;
		$this->phone = $record-> phone;
		$this->contact = $record-> contact;
		$this->discount = $record-> discount;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'company' => 'required',
		'email' => 'required',
		'phone' => 'required',
		'discount' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Provider::find($this->selected_id);
            $record->update([ 
			'company' => $this-> company,
			'email' => $this-> email,
			'phone' => $this-> phone,
			'contact' => $this-> contact,
			'discount' => $this-> discount
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Provider Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Provider::where('id', $id);
            $record->delete();
        }
    }
}
