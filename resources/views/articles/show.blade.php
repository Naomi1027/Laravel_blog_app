<x-guest-layout>

<div class="mt-20 mx-auto w-full">
    <article class="flex">
        <div class="w-40">
            <img src="{{ $article->user->icon_path }}" alt="" class="w-24 h-24 rounded-full">
        </div>
        <div>
            <div class="flex relative">
                <h2 class="mb-6 text-2xl">{{ $article->user->displayName }}</h2>
                <div class="absolute top-0 right-0">
                @if ($userId === $article->user->id)
                    <div class="flex gap-12 justify-center">
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
                    <div>
                </div>
            </div>
            <p class="mb-20">{{ $article->content }}</p>
        </div>
    </article>
</div>

</x-guest-layout>
