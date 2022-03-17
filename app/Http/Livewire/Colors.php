<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Color;

class Colors extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $color, $slug;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.colors.view', [
            'colors' => Color::latest()
						->orWhere('color', 'LIKE', $keyWord)
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
		$this->color = null;
		$this->slug = null;
    }

    public function store()
    {
        $this->validate([
		'color' => 'required',
		'slug' => 'required',
        ]);

        Color::create([ 
			'color' => $this-> color,
			'slug' => $this-> slug
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Color Successfully created.');
    }

    public function edit($id)
    {
        $record = Color::findOrFail($id);

        $this->selected_id = $id; 
		$this->color = $record-> color;
		$this->slug = $record-> slug;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'color' => 'required',
		'slug' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Color::find($this->selected_id);
            $record->update([ 
			'color' => $this-> color,
			'slug' => $this-> slug
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Color Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Color::where('id', $id);
            $record->delete();
        }
    }
}
