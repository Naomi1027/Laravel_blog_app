<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keyword = $request->input('keyword');
        $articles = Article::with('user', 'tags', 'userLikes')->when($keyword, function ($query, $keyword) {
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
        $tags = Tag::pluck('name', 'id')->toArray();

        return view('articles.create', [
            'tags' => $tags,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): RedirectResponse
    {

        $id = ['user_id' => Auth::id()];
        $validated = $request->safe()->except(['tags', 'image']);

        // 記事のインスタンスを作成
        $article = new Article(array_merge($id, $validated));

        // 新しい画像アップロード処理
        if ($request->safe()->only(['image'])) {
            $this->storeImage($article, $request->file('image'));
        }
        // 記事を保存
        $article->save();

        // タグを関連付け
        if ($request->safe()->has('tags')) {
            $article->tags()->attach($request->safe()['tags']);
        }

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
        $tags = Tag::pluck('name', 'id')->toArray();
        if ($article->user_id !== Auth::id()) {
            abort(404);
        }

        return view('articles.edit', [
            'article' => $article,
            'tags' => $tags,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, int $articleId): RedirectResponse
    {
        // バリデーション済みのデータを取得
        $validated = $request->safe()->except(['tags', 'image']);
        $article = Article::findOrFail($articleId);

        // 画像削除の処理
        if ($request->boolean('is_delete_image')) {
            $this->deleteImage($article);
        }

        // 新しい画像アップロード処理
        if ($request->safe()->only(['image'])) {
            $this->deleteImage($article);
            $this->storeImage($article, $request->file('image'));
        }

        $article->update($validated);

        if ($request->safe()->has('tags')) {
            $article->tags()->sync($request->safe()['tags']);
        } else {
            $article->tags()->detach();
        }

        return redirect()->route('articles.show', ['userName' => $article->user->name, 'articleId' => $articleId]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $articleId): RedirectResponse
    {
        $article = Article::find($articleId);
        $article->delete();
        $article->tags()->detach();

        try {
            Storage::disk('s3')->delete($article->image);
        } catch (\Exception $e) {
            Log::error('画像削除に失敗しました: ' . $e->getMessage());
            // エラーが発生しても処理を継続
        }

        return redirect()->route('articles.index');
    }

    /**
     * 引数のarticleIdに紐づくarticleにLIKEする
     *
     * @param int $articleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function like(int $articleId): RedirectResponse
    {
        /**
         * @var User $authUser
         */
        $authUser = Auth::user();
        $article = Article::findOrFail($articleId);

        // ログインユーザーがいいねした記事をすべて取得し、article_idカラムといいねしたい記事のidが存在するかを判定
        if ($authUser->articleLikes()->where('article_id', $articleId)->doesntExist()) {
            // 記事にいいねしたのがlikesテーブルにレコードが追加される
            $authUser->articleLikes()->attach($articleId);
        }

        return redirect()->route('articles.show', ['userName' => $article->user->name, 'articleId' => $articleId]);
    }

    /**
     * 引数のarticleIdに紐づくarticleにUNLIKEする
     *
     * @param int $articleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unlike(int $articleId): RedirectResponse
    {
        /**
         * @var User $authUser
         */
        $authUser = Auth::user();
        $article = Article::findOrFail($articleId);

        // ログインユーザーがいいねした記事をすべて取得し、article_idカラムといいねしたい記事のidが存在するかを判定
        if ($authUser->articleLikes()->where('article_id', $articleId)->exists()) {
            // 記事にいいねしていたのが解除される
            $authUser->articleLikes()->detach($articleId);
        }

        return redirect()->route('articles.show', ['userName' => $article->user->name, 'articleId' => $articleId]);
    }

    /**
     * Delete the existing image from storage.
     *
     * @param Article $article
     * @return void
     */
    private function deleteImage(Article $article): void
    {
        if (empty($article->image)) {
            return;
        }

        Storage::disk('s3')->delete($article->image);
        $article->image = null;
    }

    /**
     * Store a new image in storage.
     *
     * @param Article $article
     * @param string $image
     */
    private function storeImage(Article $article, $image): void
    {
        $article->image = Storage::disk('s3')->put('/images', $image, 'public');
    }
}
