<x-guest-layout>

<div class="mt-20 mx-auto w-full">
    <article class="flex">
        <div class="w-40">
            <img src="{{ $article->user->icon_path }}" alt="" class="w-24 h-24 rounded-full">
        </div>
        <div class="w-full">
            <div class="flex relative">
                <h2 class="mb-6 text-2xl">{{ $article->user->display_name }}</h2>
                <div class="absolute top-0 right-0">
                    @if (Auth::id() === $article->user->id)
                    <div class="flex-col space-y-2 ml-auto">
                        <a href="{{ route('articles.edit', ['userName' => $article->user->name, 'articleId' => $article->id]) }}" class=" w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">編集する</a>
                        <form method="POST" action="{{ route('articles.destroy', ['articleId' => $article->id]) }}">
                            @method('delete')
                            @csrf
                            <input type="submit" value="削除する" onclick='return confirm("本当に削除しますか？")' class="cursor-pointer w-24 text-center rounded-md bg-red-700 p-2 inline-block tracking-normal text-white font-bold">
                        </form>
                    </div>
                @endif
                </div>
            </div>
            <h1 class="mb-6 text-3xl">{{ $article->title }}</h1>
            <div>
                @if ( $article->tags()->exists())
                @foreach ( $article->tags as $tag )
                <p class="text-xm mb-4 mr-4 p-2 rounded bg-gray-300 inline-block">{{ $tag->name }}</p>
                @endforeach
                @endif
                <div class="mb-4 text-base flex gap-3 text-gray-600">
                    <div>
                        <span class="mr-1">最終更新日</span>
                        <span>{{ $article->updated_at->format('Y年m月d日') }}</span>
                    </div>
                    <div>
                        <span class="mr-1">投稿日</span>
                        <span>{{ $article->created_at->format('Y年m月d日') }}</span>
                    </div>
                </div>
            </div>
            <p class="mb-20">{{ $article->content }}</p>
        </div>
    </article>
    @foreach ( $article->comments as $comment )
    <section class="border-2 border-gray-400 mt-4 p-8">
        <h3 class="text-2xl ml-4 mb-4">コメント</h3>
        <div class="flex mb-4">
            <img src="{{ $comment->user->icon_path }}" alt="" class="w-24 h-24 rounded-full">
            <h2 class="mb-6 ml-4 text-2xl">{{ $comment->user->display_name }}</h2>
            @if (Auth::id() === $comment->user_id)
            <div class="flex-col space-y-2 ml-auto">
                <p>{{ $comment->updated_at->format('Y年m月d日') }}</p>
                <a href="{{ route('comments.edit', ['commentId' => $comment->id]) }}" class=" h-10 w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">編集する</a>
                <form method="POST" action="{{ route('comments.destroy', ['commentId' => $comment->id]) }}">
                    @method('delete')
                    @csrf
                    <input type="submit" value="削除する" onclick='return confirm("本当に削除しますか？")' class="cursor-pointer w-24 text-center rounded-md bg-red-700 p-2 inline-block tracking-normal text-white font-bold">
                </form>
            </div>
            @endif
        </div>
        <p>{{ $comment->comment }}</p>
    </section>
    @endforeach
    @auth
    <section class="border-2 border-gray-400 mt-4 p-8">
        <div class="flex mb-4">
            <img src="{{ $authUser->icon_path }}" alt="" class="w-24 h-24 rounded-full">
            <h3 class="text-2xl ml-4 mb-4">コメントする</h3>
        </div>
        <form method="POST" action="{{ route('comments.store', ['userName' => $article->user->name, 'articleId' => $article->id]) }}">
            @csrf
            <div class="mb-6">
                <label for="comment"></label>
                <textarea name="comment" id="comment" rows="5" class="w-full border-solid border-2 p-2 text-xl">{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="text-red-700">{{ $message }}</p>
                @enderror
                <div class="relative h-10">
                <button class="absolute inset-y-0 right-0 w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold"type="submit" value="投稿する">投稿する</button>
                </div>
            </div>
        </form>
    </section>
    @endauth
</div>

</x-guest-layout>
