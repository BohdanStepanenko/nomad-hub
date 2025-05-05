<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google’s OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle the callback from Google.
     * @throws \Exception
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()),
                    'email_verified_at' => now(),
                ]);
            } else {
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            }

            $frontendUrl = env('FRONTEND_URL');
            $token = $user->createToken('authToken')->accessToken;

            return redirect($frontendUrl . '/google/callback?token=' . $token);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Google Authorization Failed: ' . $e->getMessage());
            $frontendUrl = env('FRONTEND_URL');

            return redirect($frontendUrl . '/login?error=' . urlencode($e->getMessage()));
        }
    }
}
