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
                        <!-- プレビュー用画像の要素 -->
                        <img id="currentImage" src="{{ old('image_preview') }}" alt="選択された画像" class="w-48 h-48 object-cover border mb-4" style="display: {{ old('image_preview') ? 'block' : 'none' }};">
                        <p id="imageMessage" style="display: {{ old('image_preview') ? 'block' : 'none' }};">選択された画像です。</p>
                    </div>
                    <input type="file" id="image" name="image" class="w-full border-solid border-2 p-2 text-xl">
                    <p id="fileError" class="text-red-700" style="display: none;">ファイルサイズが大きすぎます。2MB以下のファイルを選択してください。</p>
                    <input type="hidden" id="image_preview" name="image_preview" value="{{ old('image_preview') }}">
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
            const fileInput = document.getElementById('image');
            const fileError = document.getElementById('fileError');
            const previewImage = document.getElementById('currentImage');
            const imageMessage = document.getElementById('imageMessage');
            const imagePreviewInput = document.getElementById('image_preview');
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            // 画像のプレビュー表示
            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];

                if (file) {
                    if (file.size > maxSize) {
                        fileError.style.display = 'block';
                        previewImage.style.display = 'none';
                        imageMessage.style.display = 'none';
                        imagePreviewInput.value = ''; // プレビュー情報をクリア
                    } else {
                        fileError.style.display = 'none';

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewImage.src = e.target.result;
                            previewImage.style.display = 'block';
                            imageMessage.style.display = 'block';
                            imagePreviewInput.value = e.target.result; // プレビュー情報を保持
                        };
                        reader.readAsDataURL(file);
                    }
                } else {
                    previewImage.style.display = 'none';
                    imageMessage.style.display = 'none';
                    imagePreviewInput.value = ''; // プレビュー情報をクリア
                }
            });

            // フォーム送信時の画像サイズ検証
            document.getElementById('articleForm').addEventListener('submit', function (event) {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    if (file.size > maxSize) {
                        event.preventDefault(); // フォーム送信をキャンセル
                        fileError.style.display = 'block'; // エラーメッセージを表示
                    }
                }
            });
        });
    </script>
</x-app-layout>
