<?php

namespace App\Services\User;

use App\Mail\VerificationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProfileService
{
    /**
     * @throws \Exception
     */
    public function updateName(string $updatedName): JsonResponse
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $user->update([
                'name' => $updatedName,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Name updated successfully',
                'user' => $user,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User name update error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function updateEmail(string $updatedEmail): JsonResponse
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $user->update([
                'email' => $updatedEmail,
                'verification_token' => Str::random(32),
                'email_verified_at' => null,
            ]);

            Mail::to($updatedEmail)->send(new VerificationMail($user));

            DB::commit();

            return response()->json([
                'message' => 'Email updated successfully. Check your email to verify',
                'user' => $user,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User email update error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
