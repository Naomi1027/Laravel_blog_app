<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
        ]);

        // ユーザーをログインさせる
        Auth::login($user, true);

        // ホームページへリダイレクト
        return redirect()->route('articles.index');
    }
}
