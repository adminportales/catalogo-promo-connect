<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'types';

    protected $fillable = ['type','slug'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product', 'type_id', 'id');
    }
    
}