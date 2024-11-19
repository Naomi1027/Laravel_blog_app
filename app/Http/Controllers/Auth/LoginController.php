<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

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
            $user = Socialite::driver("google")->user();
            Log::info('Google user data:', (array)$user);
            $findUser = User::where("google_id", $user->id)->first();

            if ($findUser) {
                Auth::login($findUser);
                return redirect()->intended("dashboard");
            } else {
                $newUser = User::create([
                    "name" => $user->name,
                    "email" => $user->email,
                    "google_id" => $user->id,
                    "email_verified_at" => now(),
                ]);

                Auth::login($newUser);

                return redirect()->intended("dashboard");
            }
        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
    return redirect('/login')->with('error', 'ログイン中に問題が発生しました。再度お試しください。');
        }
    }
}
