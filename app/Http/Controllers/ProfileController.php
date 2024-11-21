<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // フォームに入力された値を取得
        $updateUser = $request->user()->fill($request->validated());

        // icon_pathの入力がない場合はアイコンの更新は行わない、icon_pathがある場合は、その画像をS3に上書き保存する
        if (!is_null($updateUser->icon_path)) {
            $oldPath = str_replace(Storage::disk('s3')->url('/'), '', $updateUser->icon_path);
            Storage::disk('s3')->delete($oldPath); // S3から削除
        }
        // 新しい画像をS3にアップロードしてパスを保存
        $path = Storage::disk('s3')->put('/images', $request->file('icon_path'), 'public');
        $updateUser->icon_path = Storage::disk('s3')->url($path);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $updateUser->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
