<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'icon-path'=>['nullable', 'image'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // icon_pathがない場合は、デフォルトのuser_default.pngをDBに保存、icon_pathがある場合は、その画像を保存
        if (request()->file('icon_path') === null) {
            // ファイルがない場合はデフォルト画像を使用
            $iconPath = null;
        } else {
            // AWSのS3のimagesディレクトリに保存
            $path = Storage::disk('s3')->put('/images', request()->file('icon_path'), 'public');
            // アップロードした画像のフルパスを取得
            $iconPath = Storage::disk('s3')->url($path);
        }

        $user = User::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'email' => $request->email,
            'icon_path' => $iconPath,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
