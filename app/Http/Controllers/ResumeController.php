<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResumeResource;
use App\Models\Education;
use App\Models\Portfolio;
use App\Models\Resume;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resume = Resume::with("resumeFiles")->where("user_id", auth()->id())->first();
        if(!$resume) {
            Resume::create([
                "overview" => "Hi",
                "user_id" => auth()->id(),
            ]);
            $resume = Resume::with("resumeFiles")->where("user_id", auth()->id())->first();
        }

        $data = new ResumeResource($resume);
        return okResponse("resume fetched", $data);
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
    public function update(Request $request)
    {
        $resume = Resume::where("user_id", auth()->user()->id)->first();
        if($request->overview) {
            $resume->overview = $request->overview;
        }

        if($request->file("intro")){
            $fileName = time() . '_' . $request->file('intro')->getClientOriginalName();
            $request->file('intro')->move(public_path('uploads/'.auth()->user()->id.'/'), $fileName);
            $resume->intro = "/uploads/".auth()->user()->id.'/'.$fileName;
        }
        if($request->file("cv")){
            $fileName = time() . '_' . $request->file('cv')->getClientOriginalName();
            $request->file('cv')->move(public_path('uploads/'.auth()->user()->id.'/'), $fileName);
            $resume->resume = "/uploads/".auth()->user()->id.'/'.$fileName;
            $resume->resume_title = $request->file('cv')->getClientOriginalName();
        }
        if($request->file("portfolio")){
            $fileName = time() . '_' . $request->file('portfolio')->getClientOriginalName();
            $request->file('portfolio')->move(public_path('uploads/'.auth()->user()->id.'/'), $fileName);
            $p = "/uploads/".auth()->user()->id.'/'.$fileName;
            Portfolio::create([
                "user_id" => auth()->user()->id,
                "resume_id" => $resume->id,
                "file_url" => $p,
            ]);
        }
        
        if($request->education) {
            try {
                Education::where("resume_id", $resume->id)->delete();
                $educations = $request->education;
                foreach ($educations as $key => $edu) {
                    Education::create([
                        "title" => $edu["title"],
                        "academy" => $edu["academy"],
                        "year" => $edu["year"],
                        "user_id" => auth()->user()->id,
                        "resume_id" => $resume->id,
                    ]);
                }
            } catch (\Throwable $th) {
                return response($th->getMessage());
            }
        }
        $resume->skills = $request->skills ? $request->skills : $resume->skills;
        $resume->save();
        return okResponse("done");
    }

    
    public function removeResume(Request $request)
    {
        $resume = Resume::where("user_id", auth()->user()->id)->first();
        $resume->resume = "";
        $resume->resume_title = "";
        $resume->save();
        return okResponse("done");
    }
    public function updateIntro(Request $request)
    {
        $resume = Resume::where("user_id", auth()->user()->id)->first();
        $prev = $resume->intro;
        if($prev) {

        }
        $resume->intro = "";
        $resume->save();
        return okResponse("done");
    }

    public function deletePortfolio($id)
    {
        Portfolio::where("id", $id)->delete();
        return okResponse("done");
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
