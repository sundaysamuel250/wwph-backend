<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\JobDepartment;
use App\Models\JobType;
use App\Models\WwphJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = $request->query("title");
        $status = $request->query("status") ? $request->query("status") : "";
        $jobType = $request->query("jobType");
        $jobs = WwphJob::where("status", "!=", "deleted")->where("company_id", auth()->user()->id);
        if($status) {
            $jobs = $jobs->where("status", $status);
        }
        if($title != "") $jobs = $jobs->where("title", "LIKE", "%".$title."%");
        if($jobType != "") {
            $thJobType = JobType::where("title", $jobType)->first();
            if($thJobType) {
                $jobs = $jobs->where("job_type", $thJobType->id);
            }
        }
        $jobs = $jobs->orderBy("id", "DESC")->get();
        $recent = JobResource::collection($jobs);

        return okResponse("fetched jobs".auth()->id(), $recent);
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

    public function deleteJob($id)
    {
        $job = WwphJob::where("id", $id)->where("company_id", auth()->user()->id)->delete();
        return okResponse("Job deleted");
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
        $validated = $request->all();
        $validator = Validator::make($validated, [
            'title' => 'required|string',
            'description' => 'required|string|min:3',
            'work_type' => 'required',
            'job_type' => 'required',
            'category' => 'required',
            'salary' => 'required',
            'budget' => 'required',
            'experience' => 'required',
            'requirements' => 'required',
            'job_cover' => '',
            'skills' => 'required ',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()) {
            $erro = json_decode($validator->errors(), true);
            $msg = array_values($erro)[0];
            return errorResponse($msg[0], $erro);
        }

        $job = WwphJob::create([
            'title' => $request->title,
            'slug' => $this->jobSlug($request->title),
            'description' => $request->description,
            'work_type' => $request->work_type,
            'job_type' => $request->job_type,
            'salary' => $request->budget,
            'salary_narration' => $request->salary,
            'job_role' => $request->title,
            'experience' => $request->experience,
            'requirements' => $request->requirements,
            // 'education' => 'required ',
            'job_cover' => "cover",
            'skills' => $request->skills,
            'application_link' => env("APP_URL"),
            "closing_date" => Carbon::now()->addMonths(3),
            'company_id' => auth()->user()->id,
            'location' => $request->state . ', ' .$request->country,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
        ]);
        JobDepartment::create([
            "wwph_job_id" => $job->id,
            "department_id" => $request->category
        ]);

        return okResponse("job created");
    }

    public function jobSlug($title) {
        $slug = \Illuminate\Support\Str::slug($title);
        $isJob = WwphJob::where("slug", $slug)->first();
        if(!$isJob) {
            return $slug;
        }
        $title = $title . substr(str_shuffle("abcdefghijlmnopqrstuvwxyz"), 0, 4);
        return $this->jobSlug($title);
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
