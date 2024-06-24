<x-guest-layout>

<div class="flex flex-col gap-2">
    @foreach ( $articles as $article )
    <article class="w-full rounded-lg border-gray-400 bg-stone-100 p-4 border-2 flex">
        <div class="mr-8">
            <img src="{{ $article->user->icon_path }}" alt="" class="w-24 h-24 rounded-full">
        </div>
        <div>
            <div>
                <p class="text-xm mb-4 inline-block">{{ $article->user->name }}</p>
            </div>
            <span class="text-xs mb-4 inline-block">{{ $article->created_at->format('Y年m月d日') }}</span>
            <a href="{{ route('articles.show', ['articleId' => $article->id]) }}"><h2 class="text-md font-bold">{{ $article->title }}</h2></a>
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
