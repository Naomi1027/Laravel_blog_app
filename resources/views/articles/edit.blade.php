<x-app-layout>

<div class="mt-20 mx-auto w-4/5">
    <form method="POST" action="{{ route('articles.update', ['articleId' => $article->id]) }}">
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
                        <input type="checkbox" id="{{ $tagName}}" name="tags[]" value="{{ $tagId }}" @checked(in_array($tagId, old('tags', $article->tags->pluck('id')->toArray())))>
                        <label for="{{ $tagName }}" class="text-xl pr-2">{{ $tagName }}</label>
                    @endforeach
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
        </div>
        <div class="flex gap-12 justify-center">
            <a href="{{ route('articles.show', ['userName' => $article->user->name, 'articleId' => $article->id ]) }}" class="w-24 text-center rounded-md bg-blue-700 p-2 inline-block tracking-normal text-white font-bold">戻る</a>
            <button class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold"type="submit" value="投稿する">更新する</button>
        </div>
    </form>
</div>

</x-app-layout>
