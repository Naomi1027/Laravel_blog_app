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
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver("google")->user();
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
            Log::error($e);
            throw $e->getMessage();
        }
    }
}
