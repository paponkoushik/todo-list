<?php
namespace Tests;

use App\Models\User;

trait CreatesUsers
{
    public function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        return $user->createToken('API Token')->plainTextToken;
    }
}