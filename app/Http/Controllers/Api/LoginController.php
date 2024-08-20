<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
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
    public function login(LoginRequest $request): Response
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        // ユーザーが存在しない場合
        if ($user === null) {
            return response()->json([
                'message' => '登録して下さい!',
            ], 401);
            // 認証が成功した場合
        } elseif ($user->email_verified_at !== null && Auth::attempt($credentials)) {
            // セッションIDの生成
            $request->session()->regenerate();

            return response()->json([
                'message' => 'ログインに成功しました!',
                'user' => auth()->user(),
            ], Response::HTTP_OK);
        } else {
            // メール認証が済んでいない場合
            return response()->json([
                'message' => 'ログインに失敗しました!',
            ], 401);
        }
    }
}
