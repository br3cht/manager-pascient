<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'patient@example.com',
            'password' => 'secret-password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'patient@example.com',
            'password' => 'secret-password',
            'device' => 'feature-test',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['token']);

        $token = PersonalAccessToken::findToken($response->json('token'));

        $this->assertNotNull($token);
        $this->assertTrue($token->tokenable->is($user));
        $this->assertSame('feature-test', $token->name);
    }

    public function test_login_requires_valid_payload(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password', 'device'])
            ->assertJsonPath('errors.email.0', 'O campo e-mail deve ser um e-mail válido.')
            ->assertJsonPath('errors.password.0', 'O campo senha é obrigatório.')
            ->assertJsonPath('errors.device.0', 'O campo dispositivo é obrigatório.');
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'patient@example.com',
            'password' => 'secret-password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'patient@example.com',
            'password' => 'wrong-password',
            'device' => 'feature-test',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_authenticated_user_can_logout_and_delete_tokens(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('feature-test')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->postJson('/api/logout');

        $response
            ->assertOk()
            ->assertJson(['message' => 'Usuario desconectado']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
