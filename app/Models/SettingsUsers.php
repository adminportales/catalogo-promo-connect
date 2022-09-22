<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsUsers extends Model
{
    use HasFactory;
    protected $fillable = [
        'utility',
    ];
}
