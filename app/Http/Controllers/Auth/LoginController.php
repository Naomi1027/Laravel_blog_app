<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    // Googleログインページへリダイレクト
    public function redirectToGoogle(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): \Illuminate\Http\RedirectResponse
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $$driver */
        $driver = Socialite::driver('google');
        /** @var User $user */
        $user = $driver->stateless()->user();

        $findUser = User::where('google_id', $user->id)->first();

        if ($findUser) {
            Auth::login($findUser);

            return redirect('/dashboard');
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'email_verified_at' => now(),
            ]);

            Auth::login($newUser);

            return redirect('/dashboard');
        }
    }
}
