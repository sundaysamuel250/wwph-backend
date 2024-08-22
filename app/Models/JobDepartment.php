<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDepartment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function Jobs () {
        return $this->hasMany(WwphJob::class, "id", "wwph_job_id")->where("status", "active");
    }
    public function Department () {
        return $this->belongsTo(Department::class)->where("status", "active");
    }
}
