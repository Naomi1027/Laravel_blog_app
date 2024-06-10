@extends('layouts.app')

@section('content')

<div class="flex flex-col gap-2">
    @foreach ( $articles as $article )
    <article class="w-full rounded-lg border-gray-400 bg-stone-100 p-4 border-2">
        <span class="text-xs mb-4 inline-block">{{ $article->created_at->format('Y年m月d日') }}</span>
        <a href="{{ route('articles.show', ['articleId' => $article->id]) }}"><h2 class="text-md font-bold">{{ $article->title }}</h2></a>
    </article>
    @endforeach
</div>

<div class="text-center mt-4">
    <a href="{{ route('articles.create')}}" class="w-24 text-center rounded-md bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">投稿する</a>
</div>

@endsection
