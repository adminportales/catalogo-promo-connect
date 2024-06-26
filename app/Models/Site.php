<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'sites';

    protected $fillable = ['name','utility','woocommerce','url','consumer_key','consumer_secret'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sitesProducts()
    {
        return $this->belongsToMany(Product::class, 'sites_products', 'site_id', 'product_id');
    }

}
