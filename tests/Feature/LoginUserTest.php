<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard'); // ou '/' selon ta config

        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302); // redirection après échec login

        $this->assertGuest('filament'); // ou 'web' si c’est ton guard
    }

}
