<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_can_be_verified_using_the_email_verify_api_v1_route(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $token = 123456;
        $password_reset = DB::table('api_verify_email_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->postJson('api/v1/email/verify', [
            'token' => $token,
        ]);

        Event::assertDispatched(Verified::class);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        // $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) =>
                $json->where('code', "email_verified")
                    ->where('message', 'Email is verified')
            );

    }

    public function test_email_can_not_be_verified_using_the_email_verify_api_v1_route_with_wrong_pin(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $token = 123456;
        $password_reset = DB::table('api_verify_email_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->postJson('api/v1/email/verify', [
            'token' => "111111",
        ]);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(400);
        // $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
    }

    public function test_email_verification_can_be_resent_using_the_email_resend_token_api_v1_route(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Mail::fake();

        Mail::assertNothingSent();

        $response = $this->postJson('api/v1/resend/email/token', [
            'email' => $user->email,
        ]);

        Mail::assertSent(VerifyEmail::class);

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) =>
                $json->where('success', 'success')
                    ->where('message', 'A verification mail has been resent')
            );

        // $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
    }

}
