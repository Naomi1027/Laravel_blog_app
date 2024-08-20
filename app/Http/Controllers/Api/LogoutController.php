<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    /**
     * ログアウト処理を行うFunction
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        // ログインしていない場合
        if (Auth::check() === false) {
            return response()->json([
                'message' => 'ログインしていません!',
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            // ログインしている場合
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'ログアウトしました!',
            ], Response::HTTP_OK);
        }
    }
}
