<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DinamycPrice extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'dinamyc_price';

    protected $fillable = ['type','provider_change','type_change','amount','product_id','site_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }

}
