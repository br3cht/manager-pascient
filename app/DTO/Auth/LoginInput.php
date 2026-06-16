<?php

namespace App\DTO\Auth;

class LoginInput
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $device
    ) {}

    public static function createFromArray(array $data)
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            device: $data['device']
        );
    }
}
