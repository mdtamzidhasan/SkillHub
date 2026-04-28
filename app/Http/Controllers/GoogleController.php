<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

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
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update([
                'google_id'     => $googleUser->getId(),
                'avatar'        => $googleUser->getAvatar(),
                'access_token'  => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
            ]);
        } else {
            $user = User::create([
                'name'          => $googleUser->getName(),
                'email'         => $googleUser->getEmail(),
                'google_id'     => $googleUser->getId(),
                'avatar'        => $googleUser->getAvatar(),
                'password'      => null,
                'access_token'  => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}