<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\JobType;
use App\Models\WorkType;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function resource() {
        $workType =  WorkType::where("status", "active")->get();
        $jobtype =  JobType::where("status", "active")->get();
        $departments =  Department::where("status", "active")->get();

        return okResponse("resources fetched", [
            "work_types" => $workType,
            "job_type" => $jobtype,
            "departments" => $departments,
        ]);
    }
}
