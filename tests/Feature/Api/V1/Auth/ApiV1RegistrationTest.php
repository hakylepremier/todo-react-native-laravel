<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Mail\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiV1RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register_using_the_register_api_v1_route(): void
    {
        Mail::fake();

        Mail::assertNothingSent();

        $response = $this->postJson('api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'Test device',
        ]);

        Mail::assertSent(VerifyEmail::class);

        $response->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) =>
                $json->where('code', "register_success")
                    ->where('message', 'Successful created user. Please check your email for a 6-digit pin to verify your email.')
                    ->where('device_name', 'Test device')
                    ->has('token')
            );
    }

    public function test_new_users_can_not_register_using_the_register_api_v1_route_with_invalid_device_name(): void
    {
        $response = $this->postJson('api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => null,
        ]);

        // $rest = $response->toArray();

        $response->assertUnprocessable();

        // $this->assertGuest();
    }
}
