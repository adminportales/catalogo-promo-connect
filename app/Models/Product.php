<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'products';

    protected $fillable = [
        'internal_sku',
        'sku_parent',
        'sku',
        'name',
        'price',
        'description',
        'producto_promocion',
        'producto_nuevo',
        'precio_unico',
        'stock',
        'type_id',
        'color_id',
        'provider_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dinamycPrices()
    {
        return $this->hasMany('App\Models\DinamycPrice', 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productAttributes()
    {
        return $this->hasMany('App\Models\ProductAttribute', 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productCategories()
    {
        return $this->hasMany('App\Models\ProductCategory', 'product_id', 'id');
    }

    public function precios()
    {
        return $this->hasMany('App\Models\Price', 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->hasOne('App\Models\Provider', 'id', 'provider_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sitesProducts()
    {
        return $this->belongsToMany(Site::class, 'sites_products', 'product_id', 'site_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne('App\Models\Type', 'id', 'type_id');
    }
}
