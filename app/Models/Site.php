<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'sites';

    protected $fillable = ['name'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sitesProducts()
    {
        return $this->hasMany('App\Models\SitesProduct', 'site_id', 'id');
    }
    
}
