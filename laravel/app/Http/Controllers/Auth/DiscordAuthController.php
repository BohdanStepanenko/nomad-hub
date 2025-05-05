<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class DiscordAuthController extends Controller
{
    /**
     * Redirect the user to Discord’s OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('discord')->stateless()->redirect();
    }

    /**
     * Handle the callback from Discord.
     * @throws \Exception
     */
    public function callback(): JsonResponse
    {
        try {
            $discordUser = Socialite::driver('discord')->stateless()->user();

            $user = User::where('discord_id', $discordUser->getId())
                ->orWhere('email', $discordUser->getEmail())
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $discordUser->getName(),
                    'email' => $discordUser->getEmail(),
                    'discord_id' => $discordUser->getId(),
                    'password' => Hash::make(uniqid()),
                    'email_verified_at' => now(),
                ]);
            } else {
                if (!$user->discord_id) {
                    $user->discord_id = $discordUser->getId();
                    $user->save();
                }
            }

            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Discord Authorization Failed: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
