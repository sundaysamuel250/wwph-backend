<?php

namespace App\Http\Controllers;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
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
        $validated = $request->all();
        $validator = Validator::make($validated, [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            $erro = json_decode($validator->errors(), true);
            $msg = array_values($erro)[0];
            return errorResponse($msg[0], $erro);
        }
        $isNewsletter = Newsletter::where("email", $request->email)->first();
        if($isNewsletter) {
            $isNewsletter->status = "active";
            $isNewsletter->save();
        }else {
            Newsletter::create([
                "email" => $request->email
            ]);
        }
        return okResponse("Thank you for subscribing to out Newsletters");
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
