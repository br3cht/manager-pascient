<?php

namespace App\Http\Controllers;

use App\DTO\Auth\LoginInput;
use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(AuthRequest $authRequest)
    {
        $dataRequest = $authRequest->validated();
        $input = LoginInput::createFromArray($dataRequest);
        $token = $this->authService->login($input);

        return response()->json(data: [
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $this->authService->logout($user);

        return response()->json(data: ['message' => 'Usuario desconectado']);
    }
}
