<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded = [];

    public function providers()
    {
        return  $this->belongsToMany(Provider::class, 'role_provider', 'role_id', 'provider_id');
    }
}
