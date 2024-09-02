<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function transaction () {
        $payments = Payment::where("user_id", auth()->user()->id)->get();
        return okResponse("Payments", $payments);
    }
    public function makePayment(Request $request) {
        $validated = $request->all();
        $validator = Validator::make($validated, [
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            $erro = json_decode($validator->errors(), true);
            $msg = array_values($erro)[0];
            return errorResponse($msg[0], $erro);
        }
        try {
            $reference = $this->generateReference();
            Payment::create([
                "reference" => $reference,
                'amount' => $request->amount,
                'user_id' => auth()->user()->id,
            ]);
            return okResponse("Payment created", [
                "reference" => $reference
            ]);
        } catch (\Throwable $th) {
            return errorResponse("Unable to create payment");
        }
    }
    public function verifyPayment(Request $request) {
        $validated = $request->all();
        $validator = Validator::make($validated, [
            'amount' => 'required',
            'reference' => "required",
        ]);
        $payment = Payment::where("reference", $request->reference)->where("status", "pending")->first();
        if(!$payment) {
            return errorResponse("Payment not found");
        }
        $amt = $payment->amount;
        $user = auth()->user();
        $payment->save();
        $user->wallet = floatval($user->wallet) + floatval($amt);
        $user->save();
        return okResponse("Payment done");
    }


    protected function generateReference() {
        $ref = "WWPH".substr(str_shuffle("123456789123567890"), 0, 10);
        
        if(Payment::where("reference", $ref)->first()) {
            return $this->generateReference();
        }
        return $ref;
    }

}
