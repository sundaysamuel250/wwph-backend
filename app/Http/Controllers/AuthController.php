<?php

namespace App\Http\Controllers;

use App\Events\ForgotPasswordEvent;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use App\Models\PasswordReset;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'uploadfile', 'register', 'resendVerificationEmail', 'forgotPassword', 'resetPassword']]);
    }

    public function register(Request $request)
    {
        $validated = $request->all();
        $validator = Validator::make($validated, [
            'email' => 'required|string|email',
            'last_name' => 'string|min:3',
            'first_name' => 'string|min:3',
            'name' => 'string|min:3',
            'password' => 'required|string|min:8',
            'newsletter' => 'integer|nullable|in:1',
            'role' => 'required|string'
        ]);

        if ($validator->fails()) {
            $erro = json_decode($validator->errors(), true);
            $msg = array_values($erro)[0];


            return errorResponse($msg[0], $erro);
        }

        $newsletter = (isset($validated['newsletter'])) ? $validated['newsletter'] : 0;
        $isUser = User::where("email", $validated["email"])->first();
        if ($isUser) {
            if ($isUser->status == "deleted") {
                $userRole = Role::where("title", "User")->first();
                $isUser->update([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'password' => bcrypt($validated['password']),
                    'newsletter' => $newsletter,
                    'email_verified_at' => now(),
                    'role' => $userRole ? $userRole->id : 1
                ]);
                $isUser->status = "Active";
                $isUser->save();
            } else {
                return errorResponse("User already exist");
            }
        } else {
            $userRole = Role::where("title", $request->role)->first();
            if(!$userRole) return errorResponse("Invalid user role");
            $user = User::create([
                'email' => $validated['email'],
                'name' => $request->role == "company" ? $validated['name'] : ($validated['first_name'] . ' ' . $validated['last_name']),
                'password' => bcrypt($validated['password']),
                'newsletter' => $newsletter,
                'email_verified_at' => now(),
                'role' => $userRole ? $userRole->id : 1
            ]);
            
            // trigger welcom email to user
            $data = [
                'title' => "WELCOME TO ". env("APP_NAME") . "– WE'RE EXCITED TO HAVE YOU!",
                'to' => $user->email,
                'full_name' => $user->name,
                'body' => '
                <p> Welcome to '.env("APP_NAME").'! We’re thrilled to have you as a part of our community. Whether you\'re here to find your Dream Job, for Career tips or Hire Talent, we\'re here to support you every step of the way.</p>
        
                <p>If you have any questions or need assistance, feel free to contact our support team.</p>
                
                <p>We’re looking forward to seeing what you’ll achieve with '.env("APP_NAME").'.</p>
               ',
                'hasButton' => true,
                'buttonLink' => env('FRONTEND') . '/login',
                'buttonText' => 'My Account',
            ];
            $view = view("emails.template", ["data" => $data])->render();
            sendMail2($user->email, $data["title"], $view);

            // trigger email to admin
            $data = [
                'title' => $request->role == "company" ? "NEW COMPANY REGISTRATION ON ". env("APP_NAME") : "NEW USER REGISTRATION ON ". env("APP_NAME"),
                'to' => env("ADMIN_EMAIL"),
                'full_name' => "ADMIN",
                'body' => '
                <p>We wanted to inform you that a new user has just registered on '. env("APP_NAME") .' .</p>
        
                <p>User Details:</p>
                <ul>
                    <li>Name: '.$user->name .'</li>
                    <li>Email: '.$user->email.'</li>
                    <li>Date: '.Carbon::createFromDate($user->created_at)->format('Y-m-d H:i:s') .'</li>
                </ul>
                
                <p>We’re looking forward to seeing what you’ll achieve with '.env("APP_NAME").'.</p>
               ',
                'hasButton' => true,
                'buttonLink' => env('FRONTEND') . '/login',
                'buttonText' => 'My Account',
            ];
            $view = view("emails.template", ["data" => $data])->render();
            sendMail2(env("ADMIN_EMAIL"), $data["title"], $view);
        }

        try {
            // event(new Registered($user));
        } catch (\Exception $e) {

            return errorResponse("Failed to create user",  $e->getMessage(), 500);
        }

        return okResponse("user created");
    }



    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     operationId="login",
     *     summary="Logs in user",
     *     tags={"Users"},
     *     description="Logs in user and gives access to authenticated resources",
     *     @OA\Parameter(
     *          name="email",
     *          description="Email Field",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="password",
     *          description="Password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(response="200", description="Display a credential User."),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *       @OA\Response(
     *          response=404,
     *          description="Not found"
     *      ),
     * )
     */
    public function login(Request $request)
    {
        $validated = $request->all();
        $validator = Validator::make($validated, [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            $erro = json_decode($validator->errors(), true);
            $msg = array_values($erro)[0];


            return errorResponse($msg[0], $erro);
        }

        if (!$token = auth()->attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return errorResponse("Invalid login combination", 400);
        }

        try {
            $user = User::where('email', $validated['email'])->first();
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
        if ($user->status === "deleted") {
            return errorResponse("Account not found!");
        }

        if ($user->email_verified_at === null) {
            return errorResponse("Email not verified");
        }



        return $this->createNewToken($token);
    }

    public function createNewToken($token)
    {
        $user = User::where("id", auth()->user()->id)->first();
        $role = $user->UserRole();
        $user->role = $role->title;
        $u = $role->title == "Company" ? new CompanyResource(auth()->user()) :  new UserResource(auth()->user());
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $u
        ]);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }



    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     operationId="logout",
     *     summary="Logs out user",
     *     description="Logs out user",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent()
     *       ),
     *      security={{ "apiAuth": {} }},
     * )
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logged out']);
    }



    /**
     * @OA\Post(
     *      path="/api/v1/resend-verification",
     *      operationId="resendVerificationEmail",
     *      summary="Resend email verification email",
     *      description="Resend email verification email",
     *     @OA\Parameter(
     *          name="email",
     *          description="Email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent()
     *       ),
     *     )
     *
     */
    public function resendVerificationEmail(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email|string']);
        try {
            $user = User::where('email', $validated['email'])->first();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

        if (!isset($user)) {
            return response()->json(['message' => 'Invalid email']);
        }

        if ($user->email_verified_at !== null) {
            return response()->json(['message' => 'Email already verified']);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Mail not sent']);
        }

        return response()->json(['message' => 'Email verification mail sent']);
    }



    /**
     * @OA\Post(
     *      path="/api/v1/change-password",
     *      operationId="changePassword",
     *      tags={"Users"},
     *      summary="Change user password",
     *      description="Change user password",
     *      security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *          name="password",
     *          description="Password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Parameter(
     *          name="password_confirmation",
     *          description="Password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent()
     *       ),
     *     )
     *
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate(['password' => 'required|string|confirmed|min:8']);
        try {
            User::where('email', auth()->user()->email)
                ->update(['password' => bcrypt($validated['password'])]);
            return response()->json(['message' => 'Password updated']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Password update failed']);
        }
    }



    /**
     * @OA\Post(
     *      path="/api/v1/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"Users"},
     *      summary="Initiate password reset process",
     *      description="Send password reset link to email",
     *     @OA\Parameter(
     *          name="email",
     *          description="email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent()
     *       ),
     *     )
     *
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate(['email' => 'required|string|email']);
        try {
            $user = User::where('email', $validated['email'])->first();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        if (!isset($user)) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        $token = Str::random(40);
        $data = [
            'token' => $token,
            'email' => $validated['email'],
            'title' => 'Holiday Dialysis: Password Reset',
            'body' => 'Use the code below to reset your password'
        ];

        try {
            // event(new ForgotPasswordEvent($request->email));
            // ForgotPasswordEvent::dispatch($request->email);
            Mail::to($data['email'])->send(new PasswordResetMail($data));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

        try {
            PasswordReset::updateOrCreate(
                ['email' => $validated['email']],
                ['token' => $token],
                ['created_at' => Carbon::now()->format('Y-m-d H:i:s')]
            );
            return response()->json(['message' => 'Check your mail for the password reset link']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }



    /**
     * @OA\Put(
     *      path="/api/v1/reset-password",
     *      operationId="resetPassword",
     *      tags={"Users"},
     *      summary="Reset password",
     *      description="Reset user password",
     *     @OA\Parameter(
     *          name="email",
     *          description="email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Parameter(
     *          name="token",
     *          description="token",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Parameter(
     *          name="password",
     *          description="password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Parameter(
     *          name="password_confirmation",
     *          description="password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent()
     *       ),
     *     )
     *
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|string',
            'token' => 'required',
            'password' => 'required|string|confirmed|min:8'
        ]);

        try {
            $user = PasswordReset::where('email', $validated['email'])
                ->where('token', $validated['token'])
                ->first();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

        if (!isset($user)) {
            return response()->json(['message' => 'Wrong credentials']);
        }

        try {
            PasswordReset::where('token', $validated['token'])->delete();
            User::where('email', $validated['email'])
                ->update(['password' => bcrypt($validated['password'])]);
            return response()->json(['message' => 'Password reset successful']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Password reset failed']);
        }
    }
}
