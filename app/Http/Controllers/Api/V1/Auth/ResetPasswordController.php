<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    protected $expires;
    public function __construct()
    {
        $this->expires = 60 * 60; // 1 hour
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|size:6',
            'email' => 'required|email|exists:' . User::class,
            'password' => 'required|min:8|confirmed',
            'device_name' => 'required|string|max:255',
        ]);

        // check if token exists for this user
        $select = DB::table('api_password_reset_tokens')->where([
            ['email', $request->all()['email']],
        ])->latest()->first();

        $token = $select ? $select->token : null;

        if ($token) {
            $check = Hash::check($request->token, $token);
        } else {
            $check = false;
        }

        if (!$check) {
            return new JsonResponse(
                [
                    "message" => "The token is invalid.",
                    "errors" => [
                        "token" => [
                            "The token is invalid.",
                        ],
                    ],
                ],
                401
            );
        }

        // check if token is not expired
        $expired = Carbon::parse($select->created_at)->addSeconds($this->expires)->isPast();
        if ($expired) {
            return new JsonResponse([
                'message' => "The token has expired, request a new one",
                "errors" => [
                    "token" => [
                        "The token has expired",
                    ],
                ],
            ], 401);
        }

        // delete token
        DB::table('api_password_reset_tokens')->where([
            ['email', $request->all()['email']],
        ])->delete();

        // get user and change password
        $user = User::where('email', $request->email)->first();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->setRememberToken(Str::random(60));
        $user->save();

        // dispatch password changed event
        event(new PasswordReset($user));

        // create token to auto login user
        $token = $user->createToken($request->device_name)->plainTextToken;

        return new JsonResponse(
            [
                'code' => 'password_reset_success',
                'message' => "Your password has been reset successfully",
                'token' => $token,
                'device_name' => $request->device_name,
            ],
            200
        );
    }
}

// return new JsonResponse(['token' => $request->token, "token_from_db" => $check, 'expires' => $this->expires], 400);

// return new JsonResponse(['token' => $request->token, "token_info_from_db" => $check, "created_at" => $check->created_at, 'expires' => $this->expires, 'expired' => $expired], 200);

//         return Carbon::parse($createdAt)->addSeconds(
//     $this->throttle
// )->isFuture();

// return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();

// delete expired tokens
// $expiredAt = Carbon::now()->subSeconds($this->expires);

// $this->getTable()->where('created_at', '<', $expiredAt)->delete();
