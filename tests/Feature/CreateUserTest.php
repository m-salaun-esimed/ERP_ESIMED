<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_directly()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]); 

        
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

}
