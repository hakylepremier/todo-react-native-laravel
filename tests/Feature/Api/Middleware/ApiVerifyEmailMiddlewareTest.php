<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiVerifyEmailMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_verify_email_middleware_blocks_unverified_users(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                "email_verified_at" => null,
            ]),
            ['*']
        );

        $response = $this->getJson('/api/sample');

        $response
            ->assertStatus(403)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('code', "verify_email")
                    ->where('message', 'Please verify your email before you can continue')
            );

        // $this->assertAuthenticated();
        // $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_api_verify_email_middleware_allows_verified_users_to_visit_route(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->getJson('/api/sample');

        $response
            ->assertStatus(200);

        // $this->assertAuthenticated();
        // $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
