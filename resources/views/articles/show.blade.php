<x-guest-layout>

<div class="mt-20 mx-auto w-4/5">
    <article>
        <h1 class="mb-6 text-3xl">{{ $article->title }}</h1>
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
        <p class="mb-20">{{ $article->content }}</p>
    </article>
    <div class="flex gap-12 justify-center">
        <a href="{{ route('articles.index') }}" class="w-24 text-center rounded-md bg-blue-700 p-2 inline-block tracking-normal text-white font-bold">戻る</a>
        <a href="{{ route('articles.edit', ['articleId' => $article->id]) }}" class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">編集する</a>
        <form method="POST" action="{{ route('articles.destroy', ['articleId' => $article->id]) }}">
            @method('delete')
            @csrf
            <input type="submit" value="削除する" onclick='return confirm("本当に削除しますか？")' class="cursor-pointer w-24 text-center rounded-md bg-red-700 p-2 inline-block tracking-normal text-white font-bold">
        </form>
    </div>
</div>

</x-guest-layout>
