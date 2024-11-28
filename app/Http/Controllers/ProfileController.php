<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
        // アイコン画像を上書き保存する場合は古い画像を削除
        if(($currentIconPath = Auth::user()->icon_path) && $request->hasFile('icon_path')) {
            if (Storage::disk('s3')->exists($currentIconPath)) {
                Storage::disk('s3')->delete($currentIconPath);
            }
        }
        // フォームに入力された値を取得
        $updateUser = $request->user()->fill($request->validated());

        // フォームに画像があり、既存のアイコンがデフォルト画像の場合は、フォームの画像をS3に保存して、DBにパスを保存
        if ($request->hasFile('icon_path')) {
            $updateUser->icon_path = Storage::disk('s3')->put('/iconImages', $request->file('icon_path'), 'public');
        }
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
