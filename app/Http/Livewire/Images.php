<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Image;

class Images extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $image_url, $product_id;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.images.view', [
            'images' => Image::latest()
						->orWhere('image_url', 'LIKE', $keyWord)
						->orWhere('product_id', 'LIKE', $keyWord)
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
		$this->image_url = null;
		$this->product_id = null;
    }

    public function store()
    {
        $this->validate([
		'image_url' => 'required',
		'product_id' => 'required',
        ]);

        Image::create([ 
			'image_url' => $this-> image_url,
			'product_id' => $this-> product_id
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Image Successfully created.');
    }

    public function edit($id)
    {
        $record = Image::findOrFail($id);

        $this->selected_id = $id; 
		$this->image_url = $record-> image_url;
		$this->product_id = $record-> product_id;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'image_url' => 'required',
		'product_id' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Image::find($this->selected_id);
            $record->update([ 
			'image_url' => $this-> image_url,
			'product_id' => $this-> product_id
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Image Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Image::where('id', $id);
            $record->delete();
        }
    }
}
