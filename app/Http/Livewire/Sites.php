<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Site;

class Sites extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $name, $woocommerce, $url, $consumer_key, $consumer_secret;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.sites.view', [
            'sites' => Site::latest()
						->orWhere('name', 'LIKE', $keyWord)
						->orWhere('woocommerce', 'LIKE', $keyWord)
						->orWhere('url', 'LIKE', $keyWord)
						->orWhere('consumer_key', 'LIKE', $keyWord)
						->orWhere('consumer_secret', 'LIKE', $keyWord)
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
		$this->woocommerce = null;
		$this->url = null;
		$this->consumer_key = null;
		$this->consumer_secret = null;
    }

    public function store()
    {
        $this->validate([
		'name' => 'required',
		'woocommerce' => 'required',
		'url' => 'required',
		'consumer_key' => 'required',
		'consumer_secret' => 'required',
        ]);

        Site::create([ 
			'name' => $this-> name,
			'woocommerce' => $this-> woocommerce,
			'url' => $this-> url,
			'consumer_key' => $this-> consumer_key,
			'consumer_secret' => $this-> consumer_secret
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
		$this->woocommerce = $record-> woocommerce;
		$this->url = $record-> url;
		$this->consumer_key = $record-> consumer_key;
		$this->consumer_secret = $record-> consumer_secret;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'name' => 'required',
		'woocommerce' => 'required',
		'url' => 'required',
		'consumer_key' => 'required',
		'consumer_secret' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Site::find($this->selected_id);
            $record->update([ 
			'name' => $this-> name,
			'woocommerce' => $this-> woocommerce,
			'url' => $this-> url,
			'consumer_key' => $this-> consumer_key,
			'consumer_secret' => $this-> consumer_secret
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
