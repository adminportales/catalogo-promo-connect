<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAttribute extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'globalattributes';

    protected $fillable = ['attribute','value'];

}
