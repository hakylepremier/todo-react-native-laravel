<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:' . User::class]);

        $verify = User::where('email', $request->only('email'))->exists();

        // Retrieve the first record matching the query constraints
        //         $user = DB::table('users')->where('email', $email)->first();

        // // Update the record if it exists, or create a new one if it doesn't
        //         DB::table('users')->updateOrInsert(
        //             ['email' => $email],
        //             ['name' => $name, 'age' => $age]
        //         );

        if ($verify) {
            // create token
            $token = random_int(100000, 999999);

            // save token
            $verify2 = DB::table('api_password_reset_tokens')->where([
                ['email', $request->only(['email'])],
            ]);

            if ($verify2->exists()) {
                $verify2->delete();
            }

            // return new JsonResponse([
            //     'only' => $request->only(['email']),
            //     'all' => $request->all()['email'],
            //     'email' => $request->email,
            // ]);

            $password_reset = DB::table('api_password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);

            // $password_reset = DB::table('api_password_reset_tokens')
            //     ->insert(
            //         [
            //             'email' => $request->all()['email'],
            //             'token' => $token,
            //         ]
            //     );

            // send email
            if ($password_reset) {
                Mail::to($request->only(['email']))->send(new ResetPassword($token));

                return new JsonResponse(
                    [
                        'code' => 'password_pin',
                        'message' => "Please check your email for a 6 digit pin to rest your password",
                    ],
                    200
                );
            } else {
                return new JsonResponse(
                    [
                        'code' => '',
                        'message' => "An error has occured",
                    ],
                    500
                );
            }
        } else {
            return new JsonResponse(
                [
                    'code' => 'unknown_email',
                    'message' => "We can't find a user with that email address.",
                ],
                400
            );
        }
    }
}
