<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends Controller
{
    /**
     * Verify email
     *
     * @param EmailVerificationRequest $request
     * @return Response
     */
    public function verifyEmail(EmailVerificationRequest $request): Response
    {
        $validated = $request->validated();

        // メール認証していないユーザーを取得
        $user = User::where('email_verified_at', null)->findOrFail($validated['id']);

        // hash値を確認して一致しない場合はエラーを返す
        if (sha1($user->email) !== $validated['hash']) {
            return response()->json([
                'message' => 'Invalid hash!',
            ], Response::HTTP_BAD_REQUEST);
        }

        //markEmailAsVerified()でUserテーブルの"email_verified_at"に日付を保存
        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Successfully Verified!',
        ], Response::HTTP_OK);
    }
}
