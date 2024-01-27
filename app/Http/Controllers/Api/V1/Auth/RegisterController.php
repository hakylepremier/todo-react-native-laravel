<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            $verify2 = DB::table('api_verify_email_tokens')->where([
                ['email', $request->all()['email']],
            ]);

            if ($verify2->exists()) {
                $verify2->delete();
            }
            $pin = rand(100000, 999999);
            DB::table('api_verify_email_tokens')
                ->insert(
                    [
                        'email' => $request->all()['email'],
                        'token' => Hash::make($pin),
                    ]
                );
        }

        Mail::to($request->email)->send(new VerifyEmail($pin));

        $token = $user->createToken($request->device_name)->plainTextToken;

        return new JsonResponse(
            [
                'code' => 'register_success',
                'message' => 'Successful created user. Please check your email for a 6-digit pin to verify your email.',
                'token' => $token,
                'device_name' => $request->device_name,
            ],
            201
        );
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        // $select = DB::table('api_verify_email_tokens')
        //     ->where('email', Auth::user()->email)
        //     ->where('token', $request->token);

        $select = DB::table('api_verify_email_tokens')
            ->where('email', Auth::user()->email)->latest()->first();
        $token = $select ? $select->token : null;

        if ($token) {
            $check = Hash::check($request->token, $token);
        } else {
            $check = false;
        }

        if (!$check) {
            return new JsonResponse(
                [
                    'code' => 'pin_invalid',
                    'message' => "Invalid PIN",
                ],
                400
            );
        }

        // return ["auth" => Auth::user(), "auth_token" => $select];

        // $select = DB::table('api_verify_email_tokens')
        //     ->where('email', Auth::user()->email)
        //     ->where('token', $request->token)
        //     ->delete();

        DB::table('api_verify_email_tokens')
            ->where('email', Auth::user()->email)
            ->delete();

        $user = User::find(Auth::user()->id);
        $user->email_verified_at = Carbon::now()->getTimestamp();
        $user->save();

        event(new Verified($request->user()));

        return new JsonResponse(['code' => 'email_verified', 'message' => "Email is verified"], 200);
    }

    public function resendPin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|lowercase|email|max:255',
        ]);

        // return ["auth" => Auth::user()];

        $verify = DB::table('api_verify_email_tokens')->where([
            ['email', $request->all()['email']],
        ]);

        if ($verify->exists()) {
            $verify->delete();
        }

        $token = random_int(100000, 999999);
        $password_reset = DB::table('api_verify_email_tokens')->insert([
            'email' => $request->all()['email'],
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        if ($password_reset) {
            Mail::to($request->all()['email'])->send(new VerifyEmail($token));

            return new JsonResponse(
                [
                    'success' => "success",
                    'message' => "A verification mail has been resent",
                ],
                200
            );
        }
    }
}
