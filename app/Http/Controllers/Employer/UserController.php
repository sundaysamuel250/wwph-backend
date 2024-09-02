<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Country;
use App\Models\SocialMedia;
use App\Models\States;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

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
        $user->founded = $request->founded ? $request->founded : $user->founded;
        $user->company_size = $request->company_size ? $request->company_size : $user->company_size;
        $user->website = $request->website ? $request->website : $user->website;
        $user->phone_no = $request->phone_no ? $request->phone_no : $user->phone_no;
        $user->about_company = $request->about_company ? $request->about_company : $user->about_company;

        $user->city = $request->city ? $request->city : $user->city;
        $user->zip_code = $request->zipcode ? $request->zipcode : $user->zipcode;
        $user->company_name = $request->name;

        $user->save();
        return okResponse("profile updated");
    }

    public function show()
    {
        $id = auth()->id();
        $user = User::with(["socials", "Role"])->where("id", $id)->first();
        return new CompanyResource($user);
    }

    public function deleteAvatar()
    {
        $id = auth()->id();
        $user = auth()->user();
        if (File::exists(public_path($user->avatar))) {
            // Delete the file
            File::delete(public_path($user->avatar));
        }
        $user->avatar = null;
        $user->save();
        $user = User::with(["socials", "Role"])->where("id", $id)->first();
        return new CompanyResource($user);
    }
    public function updateSocial(Request $request, $id) {
        $val = $request->value;
        $soc = SocialMedia::where("user_id", auth()->id())->where("id", $id)->first();
        $soc->value = $val;
        $soc->save();
        return okResponse("success");
    }

    public function deleteSocial(Request $request, $id) {
        SocialMedia::where("user_id", auth()->id())->where("id", $id)->delete();
        return okResponse("success");
    }

    public function addSocial(Request $request) {
        SocialMedia::create([
            "label" => $request->label,
            "value" => $request->value,
            "user_id" => auth()->id()
        ]);
        return okResponse("added");
    }

    public function changePassword(Request $request) {
        $validated = $request->validate(['current_password' => "required", 'password' => 'required|string|confirmed|min:8']);
        try {
            $user= User::where('email', auth()->user()->email)->first();
            if(!$user){
                return errorResponse("User not found");
            }
            if(!Hash::check($validated["current_password"], $user->password))return errorResponse("Current password is invalid");
            User::where('email', auth()->user()->email)
                ->update(['password' => bcrypt($validated['password'])]);
                return okResponse("Password updated");
        } catch (\Exception $e) {
            return response()->json(['message' => 'Password update failed']);
        }
    }
    public function deleteAccount(Request $request) {
        $user= User::where('email', auth()->user()->email)->first();
        $user->status = "deleted";
        $user->save();
        return okResponse("Account deleted");
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
