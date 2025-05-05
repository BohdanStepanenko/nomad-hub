<?php

namespace App\Services\Auth;

use App\Http\Resources\UserResource;
use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    /**
     * @throws \Exception
     */
    public function register(string $email, string $password): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => $email,
                'password' => Hash::make($password),
                'verification_token' => Str::random(32),
            ]);

            $user->assignRole('Client');

            Mail::to($user->email)->send(new VerificationMail($user));

            DB::commit();

            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function login(string $email, string $password): JsonResponse
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email not verified',
            ], Response::HTTP_FORBIDDEN);
        }

        $token = $user->createToken('authToken')->accessToken;

        if (!$token) {
            return response()->json([
                'message' => 'Token generation failed',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'authToken' => $token,
            'user' => UserResource::make($user),
        ], Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function verifyEmail(string $token): JsonResponse
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid verification token',
            ], Response::HTTP_NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            $user->update([
                'email_verified_at' => now(),
                'verification_token' => null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Email verified successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Email Verification Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function resendVerification(string $email): JsonResponse
    {
        try {
            $user = User::where('email', $email)->first();

            Mail::to($user->email)->send(new VerificationMail($user));

            return response()->json([
                'message' => 'Verification resend successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Verification Resend Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        if ($token) {
            $token->revoke();

            return response()->json([
                'message' => 'You have been logged out',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Token not found. Already logged out or expired',
        ], Response::HTTP_BAD_REQUEST);
    }
}
