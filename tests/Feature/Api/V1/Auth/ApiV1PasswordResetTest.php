<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Mail\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiV1PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_pin_can_be_requested_at_forgot_password_api_v1_route(): void
    {
        Mail::fake();

        Mail::assertNothingSent();

        $user = User::factory()->create();

        $response = $this->postJson('api/v1/forgot-password', ['email' => $user->email]);

        Mail::assertSent(ResetPassword::class);

        $response->assertOk()
            ->assertJson(fn(AssertableJson $json) =>
                $json->where('code', "password_pin")
                    ->where('message', "Please check your email for a 6 digit pin to rest your password")
            );
    }

    public function test_password_can_be_reset_with_valid_token_at_reset_password_api_v1_route(): void
    {
        Mail::fake();

        Mail::assertNothingSent();

        $user = User::factory()->create();

        $token = 123456;
        DB::table('api_password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        $response = $this->postJson('api/v1/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'Test device',
        ]);

        $response->assertOk()
            ->assertJson(fn(AssertableJson $json) =>
                $json
                    ->where('code', 'password_reset_success')
                    ->where('message', 'Your password has been reset successfully')
                    ->where('device_name', 'Test device')
                    ->has('token')
            );
    }

    public function test_password_cannot_be_reset_with_invalid_token_at_reset_password_api_v1_route(): void
    {
        Mail::fake();

        Mail::assertNothingSent();

        $user = User::factory()->create();

        $token = 123456;

        $response = $this->postJson('api/v1/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'Test device',
        ]);

        $response->assertStatus(401)
            ->assertJson(fn(AssertableJson $json) =>
                $json
                    ->where('message', 'The token is invalid.')
                    ->etc()
            );
    }

    public function test_password_cannot_be_reset_with_expired_token_at_reset_password_api_v1_route(): void
    {
        Mail::fake();

        Mail::assertNothingSent();

        $user = User::factory()->create();

        $token = 123456;
        $now = Carbon::now();
        $timeTwoHoursAgo = $now->subHours(2);

        DB::table('api_password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => $timeTwoHoursAgo,
        ]);

        $response = $this->postJson('api/v1/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'Test device',
        ]);

        $response->assertStatus(401)
            ->assertJson(fn(AssertableJson $json) =>
                $json
                    ->where('message', 'The token has expired, request a new one')
                    ->etc()
            );
    }
}
