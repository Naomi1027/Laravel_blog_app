<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="articleForm" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="display_name" :value="__('ニックネーム')" />
            <x-text-input id="display_name" name="display_name" type="text" class="mt-1 block w-full" :value="old('display_name', $user->display_name)" autofocus autocomplete="display_name" />
            <x-input-error class="mt-2" :messages="$errors->get('display_name')" />
        </div>

        <div>
            <x-input-label for="icon_path" :value="__('アイコン')" />
            <div class="col-md-6">
                <input id="icon_path" type="file" name="icon_path" class="mt-1 block w-full">
                <p id="fileError" class="text-red-700" style="display: none;">ファイルサイズが大きすぎます。2MB以下のファイルを選択してください。</p>

                <!-- 既存のアイコンを表示 -->
                <div class="mt-3">
                    <img id="previewImage"
                        src="{{ $user->icon_path ? Storage::disk('s3')->url($user->icon_path) : asset('/images/user_default.png') }}"
                        alt="アイコン"
                        class="w-24 h-24 rounded-full">
                    <p id="existingIconMessage" class="text-gray-600 text-sm">
                        {{ $user->icon_path ? '現在設定されているアイコンです。' : 'デフォルトアイコンが設定されています。' }}
                    </p>
                </div>

                <x-input-error class="mt-2" :messages="$errors->get('icon_path')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        // ファイルサイズ検証スクリプトとプレビュー表示
        document.getElementById('icon_path').addEventListener('change', function(event) {
            const fileInput = event.target;
            const fileError = document.getElementById('fileError');
            const previewImage = document.getElementById('previewImage');
            const existingIconMessage = document.getElementById('existingIconMessage');
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            // ファイルが選択されている場合のみ検証
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];

                // ファイルサイズの検証
                if (file.size > maxSize) {
                    fileError.style.display = 'block'; // エラーメッセージを表示
                    fileInput.value = ''; // 入力をクリア
                    previewImage.style.display = 'none'; // プレビューを非表示
                    existingIconMessage.style.display = 'none'; // メッセージを非表示
                } else {
                    fileError.style.display = 'none'; // エラーメッセージを非表示

                    // プレビュー表示
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        existingIconMessage.textContent = '新しいアイコンが選択されました。'; // メッセージを変更
                        existingIconMessage.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                previewImage.style.display = 'none'; // ファイルが選択されていない場合はプレビューを非表示
                existingIconMessage.style.display = 'none'; // メッセージを非表示
            }
        });
    </script>
</section>
