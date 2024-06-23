<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WwphJob extends Model
{
    use HasFactory;
    public function Company() {
        return $this->belongsTo(User::class, 'company_id', 'id');
    }
    public function WorkType() {
        return $this->hasOne(WorkType::class, 'id', 'work_type');
    }
    public function JobType() {
        return $this->hasOne(JobType::class, 'id', 'job_type');
    }
}
