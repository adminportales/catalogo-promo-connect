<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = "status";
    public $timestamps = true;

    protected $fillable = [
        'name_provider',
        'status',
        'update_sumary',
    ];
}
