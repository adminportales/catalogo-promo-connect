<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'providers';

    protected $fillable = ['company','email','phone','contact','discount'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product', 'provider_id', 'id');
    }
    
}
