<?php

namespace App\Http\Livewire;

use App\Models\Provider;
use App\Models\Role;
use Livewire\Component;

class RolesProviders extends Component
{
    public $updateMode = false;
    public $providers, $roleEdit;
    public function render()
    {
        $this->providers = Provider::all();
        $roles = Role::paginate(5);
        return view('livewire.roles-providers.roles-providers', ['roles' => $roles]);
    }
    public function edit(Role $role)
    {
        $this->roleEdit = $role;
        $this->updateMode = true;
    }
    public function cancel()
    {
        $this->updateMode = false;
    }

    public function updateProvider($provider_id)
    {
        $provider = Provider::find($provider_id);
        $this->roleEdit->providers()->toggle($provider);
    }
}
