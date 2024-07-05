<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, string $userName, int $articleId): RedirectResponse
    {
        if (Article::query()->where('id', $articleId)->doesntExist()) {
            return redirect('/');
        }
        $article = Article::find($articleId);
        $validated = $request->validated();
        $comment = Comment::create(array_merge([
            'user_id' => Auth::id(),
            'article_id' => $articleId,
        ], $validated));

        return redirect()->route('articles.show', ['userName' => $article->user->name, 'articleId' => $articleId]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $commentId): View
    {
        $comment = Comment::with(['article', 'user'])->findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            abort(404);
        }

        return view('comments.edit', ['comment' => $comment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, int $commentId): RedirectResponse
    {
        if (Comment::query()->where([
            ['id', $commentId],
            ['user_id', Auth::id()],
        ])->doesntExist()) {
            return redirect('/');
        }

        $validated = $request->validated();
        Comment::where('id', $commentId)->update($validated);
        $comment = Comment::with(['user', 'article'])->findOrFail($commentId);

        return redirect()->route('articles.show', ['userName' => $comment->article->user->name, 'articleId' => $comment->article_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $commentId): RedirectResponse
    {
        if (Comment::query()->where([
            ['id', $commentId],
            ['user_id', Auth::id()],
        ])->doesntExist()) {
            return redirect('/');
        }

        $comment = Comment::find($commentId);
        $comment->delete();

        return redirect()->route('articles.show', ['userName' => $comment->article->user->name, 'articleId' => $comment->article_id]);
    }
}
