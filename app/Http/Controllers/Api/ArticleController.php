<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
}
