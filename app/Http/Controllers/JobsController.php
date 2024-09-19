<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentJobResource;
use App\Http\Resources\JobResource;
use App\Models\Department;
use App\Models\JobApplication;
use App\Models\JobDepartment;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\WwphJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function alert(Request $request)
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
        $jobs = $jobs->take(10)->get();
        $recent = JobResource::collection($jobs);

        return okResponse("fetched jobs", $recent);
    }
    public function shareJob($id)
    {
        $job = WwphJob::where("id", $id)->first();
        if(!$job) {
            return errorResponse("Job not found");
        }
        // sendMail2($request->to, "Shared Job - ". $job->title, )
        return okResponse("Job shared");
    }
    public function saved()
    {
        $savedJobs = SavedJob::where("user_id", auth()->user()->id)->get();
        $jobs = [];
        foreach ($savedJobs as $savedJob) {
            $job = WwphJob::with(["company", "jobtype", "worktype"])->where("status", "active")->where("id", $savedJob->job_id)->first();
            if($job) $jobs = [...$jobs, $job];
        }
        
        $recent = JobResource::collection($jobs);

        return okResponse("fetched saved jobs", $recent);
    }
    public function savedPost($id)
    {
        if(!SavedJob::where("job_id", $id)->where("user_id", auth()->user()->id)->first()) {

        SavedJob::create([
            "job_id" => $id,
            "user_id" => auth()->user()->id
        ]);
    }


        return okResponse("Job saved");
    }

    public function deletesaved($id)
    {
       SavedJob::where("job_id", $id)->where("user_id", auth()->user()->id)->delete();
        
        return okResponse("Job deleted");
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
        $jobs = WwphJob::where("status", "active")->where("slug", $id);
        $jobs = $jobs->first();
        if(!$jobs) return errorResponse("Job not found");
        $recent = new JobResource($jobs);
        return  okResponse("job fetched", $recent);
    }

    public function similarJobs($id)
    {
        $jobs = WwphJob::where("status", "active")->where("slug", $id);
        $jobs = $jobs->first();
        if(!$jobs) return errorResponse("Job not found");
        $myjobDepartments = JobDepartment::where("wwph_job_id", $jobs->id)->select("department_id")->distinct()->get()->toArray();
        $ids = [];
        
        foreach ($myjobDepartments as $dept) {
            $ids = [...$ids, $dept["department_id"]];
        }

        $jobDepartments = JobDepartment::with("jobs")->whereHas("jobs")->whereIn("department_id", $ids)->distinct()->get();
        $jobs = [];
        foreach ($jobDepartments as $jobdept) {
            $jobs = [...$jobs, ...$jobdept->jobs];
        }
        $jobs = JobResource::collection($jobs);
        return  okResponse("job fetched", $jobs);
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
    public function apply(Request $request)
    {
        $validated = $request->all();
        $validator = Validator::make($validated, [
            'job' => 'required|string',
        ]);

        if ($validator->fails()) {
            $erro = json_decode($validator->errors(), true);
            $msg = array_values($erro)[0];
            return errorResponse($msg[0], $erro);
        }
        $job = WwphJob::with("company")->where("id", $request->job)->first();
        if(!$job) return errorResponse("Invalid job");
        $hasApplied = JobApplication::where("job_id", $job->id)->where("candidate_id", auth()->user()->id)->first();
        if($hasApplied) {
            return errorResponse("You have applied for this Job");
        }
        JobApplication::create([
            "job_id" => $job->id,
            "candidate_id" => auth()->user()->id,
            "company_id" => $job->company->id,
        ]);
        return okResponse("Job application submitted");
    }
}
