<x-app-layout>
    <div class="mt-20 mx-auto w-4/5">
        <form method="POST" action="{{ route('articles.update', ['articleId' => $article->id]) }}" enctype="multipart/form-data" id="articleForm">
            @csrf
            <div class="w-full">
                <div class="mb-6">
                    <label for="title">タイトル</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" class="w-full border-solid border-2 p-2 text-xl">
                    @error('title')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <p class="pr-8">タグの選択</p>
                    @foreach ($tags as $tagId => $tagName)
                        <input type="checkbox" id="{{ $tagName }}" name="tags[]" value="{{ $tagId }}" class="tag-checkbox" @checked(in_array($tagId, old('tags', $article->tags->pluck('id')->toArray())))>
                        <label for="{{ $tagName }}" class="text-xl pr-2">{{ $tagName }}</label>
                    @endforeach
                    <p id="tagError" class="text-red-700" style="display: none;">最大で3つまで選択できます。</p>
                    @error('tags')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="content">本文</label>
                    <textarea name="content" id="content" rows="15" class="w-full border-solid border-2 p-2 text-xl">{{ old('content', $article->content) }}</textarea>
                    @error('content')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="image">画像</label>
                    <div class="mb-4">
                        @if ($article->image)
                        <img src="{{ Storage::disk('s3')->url("$article->image")}}" id="currentImage" alt="現在の画像" class="w-48 h-48 object-cover border mb-4">
                            <!-- 画像削除用のチェックボックスを追加 -->
                            <div>
                                <input type="checkbox" id="is_delete_image" name="is_delete_image" value="1">
                                <label for="delete_image">画像を削除する</label>
                            </div>
                        @else
                            <p class="text-black-500">画像は設定されていません。</p>
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
                <a href="{{ route('articles.show', ['userName' => $article->user->name, 'articleId' => $article->id ]) }}" class="w-24 text-center rounded-md bg-blue-700 p-2 inline-block tracking-normal text-white font-bold">戻る</a>
                <button class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold" type="submit" value="投稿する">更新する</button>
            </div>
        </form>
    </div>

    <script src="{{ asset('/js/preview.js') }}"></script>
</x-app-layout>
