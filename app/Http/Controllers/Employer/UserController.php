<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\States;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $this->validate($request, [
            
        ]);
        $user = auth()->user();
        if($request->file("file")){
            $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->move(public_path('uploads'), $fileName);
            $user->avatar = "/uploads/".$fileName;
        }
        if($request->country) {
            $isCountry = Country::where("code", $request->country)->first();
            if($isCountry) {
                $user->country = $request->code;
            }
        }

        if($request->state) {
            $isState = States::where("country_code", $request->country)->where("name", $request->state)->first();
            if($isState) {
                $user->state = $request->state;
            }
        }

        $user->name = $request->name ? $request->name : $user->name;
        $user->bio = $request->bio ? $request->bio : $user->bio;
        $user->city = $request->city ? $request->city : $user->city;
        $user->phone_no = $request->phone_no ? $request->phone_no : $user->phone_no;
        
        $user->save();
        return okResponse("profile updated");
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
