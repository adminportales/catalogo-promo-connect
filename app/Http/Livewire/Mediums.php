<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Medium;
use Illuminate\Support\Facades\Storage;

class Mediums extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $name, $path;
    public $updateMode = false;

    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return view('livewire.mediums.view', [
            'media' => Medium::latest()
                ->orWhere('name', 'LIKE', $keyWord)
                ->orWhere('path', 'LIKE', $keyWord)
                ->paginate(24),
        ]);
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Medium::where('id', $id)->first();
            Storage::delete($record->path);
            $record->delete();
        }
    }
}
