<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'avatar'            => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password'          => Hash::make(Str::random(24)),
                ]
            );

            // Jangan override role kalau sudah admin
            if (! $user->wasRecentlyCreated && $user->isAdmin()) {
                // keep existing role
            } else {
                $user->updateQuietly(['role' => $user->role ?? 'customer']);
            }

            Auth::login($user, remember: true);

            return redirect()->intended(route('home'));

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Login Google gagal. Silakan coba lagi.');
        }
    }
}
