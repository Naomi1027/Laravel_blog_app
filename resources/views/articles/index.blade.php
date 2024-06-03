@extends('layouts.app')

@section('content')

<div class="flex flex-col gap-2">
    @foreach ( [1, 2, 3, 4] as $index )
    <article class="w-full rounded-lg border-gray-400 bg-stone-100 p-4 border-2">
        <span class="text-xs mb-4 inline-block">yyyy年mm月dd日</span>
        <h2 class="text-md font-bold mb-1">タイトル</h2>
        <p class="text-md">説明文</p>
    </article>
    @endforeach
</div>

<div class="text-center mt-4">
    <a href="#" class="w-fit bg-cyan-400 p-2 inline-block tracking-normal text-white font-bold">投稿する</a>
</div>

@endsection
