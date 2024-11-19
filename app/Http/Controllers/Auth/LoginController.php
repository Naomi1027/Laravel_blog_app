<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class LoginController extends Controller
{
    // Googleログインページへリダイレクト
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
{
    try {
        $user = Socialite::driver('google')->user();

        // デバッグ用: $userをレスポンスで返す
        return response()->json(['user' => $user]);

        $findUser = User::where("google_id", $user->id)->first();

        if ($findUser) {
            Auth::login($findUser);
            return redirect()->route('dashboard');
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'email_verified_at' => now(),
            ]);

            Auth::login($newUser);

            return redirect()->route('dashboard');
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}

}
