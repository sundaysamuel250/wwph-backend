<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDepartment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function Jobs () {
        return $this->hasMany(WwphJob::class)->where("status", "active");
    }
}
