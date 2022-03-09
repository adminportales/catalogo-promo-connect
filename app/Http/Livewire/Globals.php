<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GlobalAttribute;

class Globals extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $attribute, $value;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.globalAttributes.view', [
            'globalAttributes' => GlobalAttribute::latest()
						->orWhere('attribute', 'LIKE', $keyWord)
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
		$this->attribute = null;
		$this->value = null;
    }

    public function store()
    {
        $this->validate([
		'attribute' => 'required',
		'value' => 'required',
        ]);

        GlobalAttribute::create([
			'attribute' => $this-> attribute,
			'value' => $this-> value
        ]);

        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'GlobalAttribute Successfully created.');
    }

    public function edit($id)
    {
        $record = GlobalAttribute::findOrFail($id);

        $this->selected_id = $id;
		$this->attribute = $record-> attribute;
		$this->value = $record-> value;

        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'attribute' => 'required',
		'value' => 'required',
        ]);

        if ($this->selected_id) {
			$record = GlobalAttribute::find($this->selected_id);
            $record->update([
			'attribute' => $this-> attribute,
			'value' => $this-> value
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'GlobalAttribute Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = GlobalAttribute::where('id', $id);
            $record->delete();
        }
    }
}
