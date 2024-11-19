<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    // Googleログインページへリダイレクト
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleCallback(Request $request)
    {
        try {
            $user = Socialite::driver('google')->user();
            $accessToken = $user->token;

            // Supabaseにユーザー情報を登録
            $response = Http::withHeaders([
                'apikey' => env('SUPABASE_ANON_KEY'),
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post(env('SUPABASE_URL') . '/auth/v1/signup', [
                'email' => $user->email,
                'password' => uniqid(), // Supabaseはパスワードを必要とする
            ]);

            if ($response->successful()) {
                return redirect()->route('dashboard');
            } else {
                return redirect('/login')->with('error', 'Supabase認証に失敗しました。');
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google認証に失敗しました。');
        }
    }
}

