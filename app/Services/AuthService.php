<?php

namespace App\Services;

use App\DTO\Auth\LoginInput;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private AuthRepository $authRepisitory
    ) {}

    public function login(LoginInput $input): string
    {
        $user = $this->authRepisitory->getUserByEmail($input->email);

        if (empty($user) || !Hash::check($input->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Usuario ou senha incorreta',
            ]);
        }

        return $user->createToken($input->device)->plainTextToken;
    }

    public function logout(User $user)
    {
        return $this->authRepisitory->deleteToken($user);
    }
}
