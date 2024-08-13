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
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $user = User::findOrFail($request->id);

        //markEmailAsVerified()でUserテーブルの"email_verified_at"に日付を保存
        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Successfully Verified!',
            'id' => $request->id,
            'hash' => $request->hash,
        ], Response::HTTP_OK);
    }
}
