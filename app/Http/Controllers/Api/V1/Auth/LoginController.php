<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $request->validate([
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->only(['email']))->first();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return new JsonResponse(
            [
                'message' => 'Login Succesful',
                'code' => 'login_success',
                'token' => $token,
                'device_name' => $request->device_name,
            ],
            200
        );
    }

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return new JsonResponse(
            [
                'code' => 'logout_success',
                'message' => 'Logged Out Successfully',
            ],
            200
        );
    }
}
