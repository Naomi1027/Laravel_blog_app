<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $articles = Article::all();

        return view('articles.index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $articleId): View
    {
        $article = Article::findOrFail($articleId);

        return view('articles.show', [
            'article' => $article,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): void
    {
        //
    }
}
