<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_api_v1_route(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test device',
        ]);

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) =>
                $json->where('code', "login_success")
                    ->where('message', 'Login Succesful')
                    ->where('device_name', 'Test device')
                    ->has('token')
            );

        $this->assertAuthenticated();
        // $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password_on_login_api_v1_route(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // $rest = $response->toArray();

        $response->assertUnprocessable();

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_device_name_on_login_api_v1_route(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => null,
        ]);

        // $rest = $response->toArray();

        $response->assertUnprocessable();

        // $this->assertGuest();
    }

    public function test_users_can_logout_using_logout_api_v1_route(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('/api/v1/logout');

        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 'logout_success',
                'message' => 'Logged Out Successfully',
            ]);

        // $this->assertGuest();
        // $response->assertRedirect('/');
    }
}
