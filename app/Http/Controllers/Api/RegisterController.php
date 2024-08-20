<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * 新しいユーザーを登録
     *
     * @param RegisterRequest $request
     * @return Response
     */
    public function register(RegisterRequest $request): Response
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($request->password);

        // usersテーブルにemailが一致し、email_verified_atがnullのレコードを削除
        User::where([
            ['email', $validated['email']],
            ['email_verified_at', null],
        ])->forceDelete();

        // usersテーブルに新しいユーザーを登録
        $user = User::create($validated);

        event(new Registered($user));

        return response()->json([
            'message' => '登録しました!',
            'name' => $user->name,
            'email' => $user->email,
        ], Response::HTTP_OK);
    }
}
