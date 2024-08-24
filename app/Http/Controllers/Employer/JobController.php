<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\JobType;
use App\Models\WwphJob;
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
        $jobs = WwphJob::where("company_id", auth()->user()->id);
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
        $jobs = $jobs->get();
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
            'job_type' => 'required ',
            'job_type' => 'required ',
            'category' => 'required ',
            'salary' => 'required ',
            'naration' => 'required ',
            'experience' => 'required ',
            'education' => 'required ',
            'job_cover' => 'required ',
            'benefits' => 'required ',
        ]);

        if ($validator->fails()) {
            $erro = json_decode($validator->errors(), true);
            $msg = array_values($erro)[0];
            return errorResponse($msg[0], $erro);
        }

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
