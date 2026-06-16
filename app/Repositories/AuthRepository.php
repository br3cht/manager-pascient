<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository
{
    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function deleteToken(User $user): void
    {
        $user->tokens()->delete();
    }
}
