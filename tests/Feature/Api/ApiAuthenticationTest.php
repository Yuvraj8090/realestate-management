<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_token_can_be_issued_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Broker->value,
        ]);

        $response = $this->postJson('/api/auth/tokens', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Automated Test Device',
        ]);

        $response->assertCreated()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure([
                'message',
                'token_type',
                'token',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_me_endpoint_requires_sanctum_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_me_endpoint(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::PropertyOwner->value,
        ]);

        $token = $user->createToken('API Test Token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/me');

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.role', UserRole::PropertyOwner->value);
    }
}
