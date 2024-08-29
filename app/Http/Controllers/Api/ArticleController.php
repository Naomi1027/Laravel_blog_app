<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ArticleController extends Controller
{
    /**
     * 全ての記事を取得するFunction
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        $articles = Article::with('user', 'tags', 'userLikes')->latest()->orderBy('id', 'ASC')->paginate(10);

        return ArticleResource::collection($articles);
    }

    /**
     * 記事を投稿するFunction
     *
     * @param StoreArticleRequest $request
     * @return JsonResource
     */
    public function store(StoreArticleRequest $request): JsonResource
    {
        $id = ['user_id' => Auth::id()];
        $validated = $request->safe()->except(['tags']);
        $article = Article::create(array_merge($id, $validated));

        if ($request->safe()->has('tags')) {
            $article->tags()->attach($request->safe()['tags']);
        }

        return new ArticleResource($article);
    }

    /**
     * 記事を更新するFunction
     *
     * @param StoreArticleRequest $request
     * @param int $articleId
     * @return JsonResource
     */
    public function update(StoreArticleRequest $request, int $articleId): JsonResource
    {
        $validated = $request->safe()->except(['tags']);

        // 記事IDに紐づく記事を取得
        $article = Article::where('id', $articleId)
            ->first();

        // $articleIdが存在しない場合は、404エラーを返す
        if (! $article) {
            throw new HttpException(404, 'この記事は存在しません。');
        }
        // ログインユーザーが記事の投稿者でない場合は、403エラーを返す
        if (Auth::id() !== $article->user_id) {
            throw new HttpException(403, '権限がありません。');
        }

        $article->update($validated);

        if ($request->safe()->has('tags')) {
            $article->tags()->sync($request->safe()['tags']);
        } else {
            $article->tags()->detach();
        }

        return new ArticleResource($article);
    }

    /**
     * 記事を削除するFunction
     *
     * @param int $articleId
     * @return Response
     */
    public function destroy(int $articleId): Response
    {
        // 記事IDに紐づく記事を取得
        $article = Article::find($articleId);

        // $articleIdが存在しない場合は、404エラーを返す
        if (! $article) {
            throw new HttpException(404, 'この記事は存在しません。');
        }
        // ログインユーザーが記事の投稿者でない場合は、403エラーを返す
        if (Auth::id() !== $article->user_id) {
            throw new HttpException(403, '権限がありません。');
        }
        // 記事を削除
        $article->delete();
        // 紐付くタグを削除
        $article->tags()->detach();

        return response()->json([
            'message' => '記事を削除しました。',
            'detail' => $article,
            'statusCode' => Response::HTTP_OK,
        ]);
    }
}
