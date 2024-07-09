<x-guest-layout>

<div class="flex flex-col gap-2">
    <div class="pb-2 flex justify-center">
        <form action="{{ route('articles.index') }}" method="GET">
            <input type="search" name="keyword" maxlength="30" placeholder="キーワードを入力">
            <input type="submit" class="w-16 h-10 text-center rounded-md  bg-gray-400" value="検索">
        </form>
    </div>
    @if ($articles->isEmpty())
        <p class="text-center my-20 text-xl">検索条件に一致する記事が見つかりません。</p>
    @endif
    @foreach ( $articles as $article )
    <article class="w-full rounded-lg border-gray-400 bg-stone-100 p-4 border-2 flex">
        <div class="mr-8">
            <img src="{{ $article->user->icon_path }}" alt="" class="w-24 h-24 rounded-full">
        </div>
        <div>
            <div>
                <p class="text-xm mb-4 inline-block">{{ $article->user->display_name }}</p>
            </div>
            <span class="text-xs mb-4 inline-block">{{ $article->created_at->format('Y年m月d日') }}</span>
            <a href="{{ route('articles.show', ['userName' => $article->user->name, 'articleId' => $article->id]) }}"><h2 class="text-md font-bold">{{ $article->title }}</h2></a>
        @if ( $article->tags()->exists())
            @foreach ( $article->tags as $tag )
            <p class="text-xm mt-4 mr-4 p-2 rounded bg-gray-300 inline-block">{{ $tag->name }}</p>
            @endforeach
        @endif
        </div>
    </article>
    @endforeach
</div>

<div class="text-center mt-4">
    <a href="{{ route('articles.create')}}" class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">投稿する</a>
</div>

</x-guest-layout>
