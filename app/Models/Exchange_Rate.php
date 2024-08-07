<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  Exchange_Rate extends Model
{
    use HasFactory;

    public $timestamps = true;

    public $table = 'exchange_rates';

    public $fillable = ['currency_from', 'currency_to', 'rate'];
}
