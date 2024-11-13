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
        <div>
            <div class="mr-8">
                {{-- アイコン --}}
                @if ($article->user->icon_path === null)
                    <img src="{{ asset('/images/user_default.png') }}" alt="アイコン" class="w-24 h-24 rounded-full" />
                @else
                    <img src="{{ $article->user->icon_path }}" alt="アイコン" class="w-24 h-24 rounded-full" />
                @endif
            </div>
            <div class="flex mt-4 ml-8">
                <svg class="h-8 w-8 text-yellow-500"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round">  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" /></svg>
                <p class="pt-2 pl-1">{{ $article->userLikes->count() }}</p>
            </div>
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
{{ $articles->links() }}

<div class="text-center mt-4">
    <a href="{{ route('articles.create')}}" class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">投稿する</a>
</div>

</x-guest-layout>
