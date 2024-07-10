<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keyword = $request->input('keyword');
        $articles = Article::with('user', 'tags')->when($keyword, function ($query, $keyword) {
            $query->where('title', 'like', "%{$keyword}%")
                ->orWhere('content', 'like', "%{$keyword}%");
        })->latest()->paginate(10);

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
        $id = ['user_id' => Auth::id()];
        $validated = $request->validated();
        Article::create(array_merge($id, $validated));

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $userName, int $articleId): View
    {
        $article = Article::with(['user', 'tags', 'comments.user'])->findOrFail($articleId);
        $authUser = Auth::user();
        if ($article->user->name !== $userName) {
            abort(404);
        }

        return view('articles.show', [
            'article' => $article,
            'authUser' => $authUser,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $userName, int $articleId): View
    {
        $article = Article::findOrFail($articleId);
        if ($article->user_id !== Auth::id()) {
            abort(404);
        }

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
        $article = Article::findOrFail($articleId);

        return redirect()->route('articles.show', ['userName' => $article->user->name, 'articleId' => $articleId]);
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
