<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Site;

class Sites extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $name;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.sites.view', [
            'sites' => Site::latest()
						->orWhere('name', 'LIKE', $keyWord)
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
		$this->name = null;
    }

    public function store()
    {
        $this->validate([
		'name' => 'required',
        ]);

        Site::create([ 
			'name' => $this-> name
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Site Successfully created.');
    }

    public function edit($id)
    {
        $record = Site::findOrFail($id);

        $this->selected_id = $id; 
		$this->name = $record-> name;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'name' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Site::find($this->selected_id);
            $record->update([ 
			'name' => $this-> name
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Site Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Site::where('id', $id);
            $record->delete();
        }
    }
}
