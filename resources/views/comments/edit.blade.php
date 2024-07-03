<x-app-layout>

    <div class="mt-20 mx-auto w-4/5">
        <form method="POST" action="{{ route('comments.update', ['commentId' => $comment->id]) }}">
            @csrf
            <div class="w-full">
                <div class="mb-6">
                    <img src="{{ $comment->user->icon_path }}" alt="" class="w-24 h-24 rounded-full mb-4">
                    <label for="comment">コメント</label>
                    <textarea name="comment" id="comment" rows="15" class="w-full border-solid border-2 p-2 text-xl">{{ old('comment', $comment->comment) }}</textarea>
                    @error('comment')
                        <p class="text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex gap-12 justify-center">
                <a href="{{ route('articles.show', ['userName' => $comment->user->name, 'articleId' => $comment->article_id]) }}" class="w-24 text-center rounded-md bg-blue-700 p-2 inline-block tracking-normal text-white font-bold">戻る</a>
                <button class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold"type="submit" value="投稿する">更新する</button>
            </div>
        </form>
    </div>

    </x-app-layout>
