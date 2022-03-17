<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedJobsCron extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'failed_jobs_cron';

    protected $fillable = ['name','message','status','type'];

}
