@extends('layouts.app')

@section('content')

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
        <a href="#" class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">編集する</a>
        <a href="#" class="w-24 text-center rounded-md bg-red-700 p-2 inline-block tracking-normal text-white font-bold">削除する</a>
    </div>
</div>

@endsection
