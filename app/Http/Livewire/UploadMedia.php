<?php

namespace App\Http\Livewire;

use App\Models\Medium;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadMedia extends Component
{
    use WithFileUploads;

    public $photos = [];

    public function render()
    {
        return view('livewire.mediums.upload-media');
    }

    public function save()
    {
        $this->validate([
            'photos.*' => 'image|max:2048', // 1MB Max
        ]);

        foreach ($this->photos as $photo) {
            $filePath = time() . $photo->getClientOriginalName();
            $photo->storeAs('public/photos', $filePath);
            Medium::create([
                'name' => $filePath,
                'path' => '/storage/photos/' . $filePath
            ]);
        }
        $this->photos = null;
        redirect()->route('media.index');
    }
}
