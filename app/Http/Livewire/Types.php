<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Type;

class Types extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $type, $slug;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.types.view', [
            'types' => Type::latest()
						->orWhere('type', 'LIKE', $keyWord)
						->orWhere('slug', 'LIKE', $keyWord)
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
		$this->slug = null;
    }

    public function store()
    {
        $this->validate([
		'type' => 'required',
		'slug' => 'required',
        ]);

        Type::create([ 
			'type' => $this-> type,
			'slug' => $this-> slug
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Type Successfully created.');
    }

    public function edit($id)
    {
        $record = Type::findOrFail($id);

        $this->selected_id = $id; 
		$this->type = $record-> type;
		$this->slug = $record-> slug;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'type' => 'required',
		'slug' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Type::find($this->selected_id);
            $record->update([ 
			'type' => $this-> type,
			'slug' => $this-> slug
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Type Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Type::where('id', $id);
            $record->delete();
        }
    }
}
