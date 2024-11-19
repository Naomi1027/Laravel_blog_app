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
        $googleUser = Socialite::driver('google')->stateless()->user();
        // email が合致するユーザーを取得
        $user = User::where('email', $googleUser->email)->first();
        // 見つからなければ新しくユーザーを作成
        if ($user == null) {
            $user = $this->createUserByGoogle($googleUser);
        }
        // ログイン処理
        Auth::login($user, true);
        return redirect('/dashboard');
    }

    public function createUserByGoogle($googleUser)
    {
        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'email_verified_at' => now(),
        ]);
        return redirect('/dashboard');
    }
}

