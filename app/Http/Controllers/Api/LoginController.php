<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\loginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * ログイン処理を行うFunction
     *
     * @param loginRequest $request
     * @return Response
     */
    public function login(loginRequest $request): Response
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        // 認証が成功した場合
        if ($user->email_verified_at !== null && Auth::attempt($credentials)) {
            // セッションIDの生成
            $request->session()->regenerate();

            return response()->json([
                'message' => 'ログインに成功しました!',
                'user' => auth()->user(),
            ], Response::HTTP_OK);
        }

        // 認証が失敗した場合
        return response()->json([
            'message' => 'ログインに失敗しました!',
        ], 401);
    }
}