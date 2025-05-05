<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendResetLinkRequest;
use App\Services\Auth\PasswordService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function __construct(
        public PasswordService $passwordService
    ) {
    }

    public function sendResetLink(SendResetLinkRequest $request): JsonResponse
    {
        $email = $request->input('email');

        return $this->success($this->passwordService->sendResetLink($email));
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $token = $request->input('token');

        return $this->success($this->passwordService->resetPassword($email, $password, $token));
    }
}
