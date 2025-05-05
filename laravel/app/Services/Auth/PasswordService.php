<?php

namespace App\Services\Auth;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class PasswordService
{
    public function sendResetLink(string $email): JsonResponse
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $token = app('auth.password.broker')->createToken($user);
        $resetLink = env('FRONTEND_URL') . '/reset-password?token=' . $token . '&email=' . $email;

        Mail::to($user->email)->send(new ResetPasswordMail($user, $resetLink));

        return response()->json([
            'message' => 'Password reset link sent',
        ], Response::HTTP_OK);
    }

    public function resetPassword(string $email, string $password, string $token): JsonResponse
    {
        $status = Password::reset(
            [
                'email' => $email,
                'password' => $password,
                'token' => $token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], Response::HTTP_OK)
            : response()->json(['message' => __($status)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws \Exception
     */
    public function updatePassword(string $currentPassword, string $newPassword): JsonResponse
    {
        $user = Auth::user();

        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $user->forceFill([
                'password' => Hash::make($newPassword),
            ])->save();

            $user->tokens()->delete();

            return response()->json([
                'message' => 'Password updated successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Password update error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
