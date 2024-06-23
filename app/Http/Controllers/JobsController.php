<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentJobResource;
use App\Http\Resources\JobResource;
use App\Models\Department;
use App\Models\JobDepartment;
use App\Models\JobType;
use App\Models\WwphJob;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = $request->query("title");
        $location = $request->query("location");
        $jobType = $request->query("jobType");
        $jobs = WwphJob::where("status", "active");
        if($title != "") $jobs = $jobs->where("title", "LIKE", "%".$title."%");
        if($location != "") $jobs = $jobs->where("location", "LIKE", "%".$location."%");
        if($jobType != "") {
            $thJobType = JobType::where("title", $jobType)->first();
            if($thJobType) {
                $jobs = $jobs->where("job_type", $thJobType->id);
            }
        }
        $jobs = $jobs->get();
        $recent = JobResource::collection($jobs);

        return okResponse("fetched jobs", $recent);
    }
    public function homepage()
    {
        $recent = WwphJob::with(["company", "jobtype", "worktype"])->where("status", "active")->orderBy("id", "DESC")->take(10)->get();
        $departments = Department::with("DepartmentJobs")->whereHas("DepartmentJobs")->where("status", "active")->take(5)->get();

        $recent = JobResource::collection($recent);
        $departments = DepartmentJobResource::collection($departments);
        return okResponse("jobs fetched", [
            "recent" => $recent,
            "departments" => $departments,
        ]);
    }
    public function fetchJobTypes () {
        $jobtypes = JobType::where("status", "active")->get();
        return okResponse("fetched", $jobtypes);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
