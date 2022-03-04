<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Subcategory;

class Subcategories extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $subfamily, $category_id;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.subcategories.view', [
            'subcategories' => Subcategory::latest()
						->orWhere('subfamily', 'LIKE', $keyWord)
						->orWhere('category_id', 'LIKE', $keyWord)
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
		$this->subfamily = null;
		$this->category_id = null;
    }

    public function store()
    {
        $this->validate([
		'subfamily' => 'required',
        ]);

        Subcategory::create([
			'subfamily' => $this-> subfamily,
			'category_id' => $this-> category_id
        ]);

        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Subcategory Successfully created.');
    }

    public function edit($id)
    {
        $record = Subcategory::findOrFail($id);

        $this->selected_id = $id;
		$this->subfamily = $record-> subfamily;
		$this->category_id = $record-> category_id;

        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'subfamily' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Subcategory::find($this->selected_id);
            $record->update([
			'subfamily' => $this-> subfamily,
			'category_id' => $this-> category_id
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Subcategory Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Subcategory::where('id', $id);
            $record->delete();
        }
    }
}
