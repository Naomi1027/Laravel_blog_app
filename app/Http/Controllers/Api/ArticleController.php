<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleController extends Controller
{
    /**
     * 全ての記事を取得するFunction
     */
    public function index(): JsonResource
    {
        return ArticleResource::collection(Article::with('user', 'tags', 'userLikes')->latest()->orderBy('id', 'ASC')->paginate(10));
    }
}
