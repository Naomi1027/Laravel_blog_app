<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
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
    public function create(): View
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Article::create($validated);

        return redirect('/');
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
    public function edit(int $articleId): View
    {
        $article = Article::findOrFail($articleId);

        return view('articles.edit', [
            'article' => $article,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, int $articleId): RedirectResponse
    {
        $validated = $request->validated();
        Article::where('id', $articleId)->update($validated);

        return redirect()->route('articles.show', ['articleId' => $articleId]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $articleId): RedirectResponse
    {
        $article = Article::find($articleId);
        $article->delete();

        return redirect()->route('articles.index');
    }
}
