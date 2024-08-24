<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\SavedCandidate;
use App\Models\User;
use Illuminate\Http\Request;

class SavedCandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $saved = SavedCandidate::where("company_id", auth()->user()->id)->pluck("candidate_id")->toArray();
        $saved = User::whereIn("id", $saved)->get();
        return okResponse("Saved Candadiate fetched", UserResource::collection($saved));
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
        if($request->candidate_id == "") return okResponse("Candiate saved");
        $isSaved = SavedCandidate::where("candidate_id", $request->candidate_id)->where("company_id", auth()->user()->id)->first();
        if(!$isSaved){
            SavedCandidate::create([
                "candidate_id" => $request->candidate_id,
                "company_id" => auth()->user()->id
            ]);
        } 
        return okResponse("Candiate saved");
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
        SavedCandidate::where("candidate_id", $id)->where("company_id", auth()->user()->id)->delete();
        return okResponse("Candiate deleted");        
    }
}
