<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, int $articleId): RedirectResponse
    {
        $id = ['user_id' => Auth::id(), 'article_id' => $articleId];
        $validated = $request->validated();
        $comment = Comment::create(array_merge($id, $validated));

        return redirect()->route('articles.show', ['userName' => $comment->user->name, 'articleId' => $comment->article->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $articleId, int $commentId): View
    {
        $comment = Comment::with(['article', 'user'])->findOrFail($commentId);

        return view('comments.edit', [
            'comment' => $comment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, int $commentId): RedirectResponse
    {
        $validated = $request->validated();
        Comment::where('id', $commentId)->update($validated);
        $comment = Comment::with(['user', 'article'])->findOrFail($commentId);

        return redirect()->route('articles.show', ['userName' => $comment->user->name, 'articleId' => $comment->article->id]);
    }
}
