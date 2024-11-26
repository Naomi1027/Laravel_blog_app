<x-app-layout>
    <div class="mt-20 mx-auto w-4/5">
        <form method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data" id="articleForm">
            @csrf
            <div class="w-full">
                <div class="mb-6">
                    <label for="title">タイトル</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="w-full border-solid border-2 p-2 text-xl">
                    @error('title')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <p class="pr-8">タグの選択</p>
                    @foreach ($tags as $tagId => $tagName)
                        <input type="checkbox" id="{{ $tagName }}" name="tags[]" value="{{ $tagId }}" class="tag-checkbox" @checked(is_array(old('tags')) && in_array($tagId, old('tags')))>
                        <label for="{{ $tagName }}" class="text-xl pr-2">{{ $tagName }}</label>
                    @endforeach
                    <p id="tagError" class="text-red-700" style="display: none;">最大で3つまで選択できます。</p>
                    @error('tags')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="content">本文</label>
                    <textarea name="content" id="content" rows="10" class="w-full border-solid border-2 p-2 text-xl">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="image">画像</label>
                    <div class="mb-4">
                        @php
                            $tempImagePath = session('temp_image');
                        @endphp
                        @if ($tempImagePath)
                            <img src="{{ Storage::disk('s3')->url($tempImagePath) }}" id="currentImage" alt="選択された画像" class="w-48 h-48 object-cover border mb-4">
                            <p>選択された画像です。</p>
                        @endif
                    </div>
                    <input type="file" id="image" name="image" class="w-full border-solid border-2 p-2 text-xl">
                    <p id="fileError" class="text-red-700" style="display: none;">ファイルサイズが大きすぎます。2MB以下のファイルを選択してください。</p>
                    @error('image')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex gap-12 justify-center">
                <a href="{{ route('articles.index') }}" class="w-24 text-center rounded-md bg-blue-700 p-2 inline-block tracking-normal text-white font-bold">戻る</a>
                <button class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold" type="submit" value="投稿する">投稿する</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const maxTags = 3; // 最大選択可能数
            const checkboxes = document.querySelectorAll('.tag-checkbox');
            const tagError = document.getElementById('tagError');

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const checkedCount = document.querySelectorAll('.tag-checkbox:checked').length;

                    if (checkedCount > maxTags) {
                        this.checked = false; // チェックを無効化
                        tagError.style.display = 'block'; // エラーメッセージを表示
                    } else {
                        tagError.style.display = 'none'; // エラーメッセージを非表示
                    }
                });
            });

            const fileInput = document.getElementById('image');
            const fileError = document.getElementById('fileError');
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            document.getElementById('articleForm').addEventListener('submit', function(event) {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    if (file.size > maxSize) {
                        event.preventDefault(); // フォーム送信をキャンセル
                        fileError.style.display = 'block'; // エラーメッセージを表示
                    } else {
                        fileError.style.display = 'none'; // エラーメッセージを非表示
                    }
                }
            });
        });
    </script>
</x-app-layout>
