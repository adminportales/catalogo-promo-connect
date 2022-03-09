<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAttribute extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'globalAttributes';

    protected $fillable = ['attribute','value'];

}
