<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Article $this */
        return [
            'title' => $this->title,
            'created_at' => $this->created_at,
            'display_name' => $this->user->display_name,
            'icon_path' => $this->user->icon_path,
            'tags' => TagResource::collection($this->tags),
            'number_of_likes' => $this->userLikes->count(),
        ];
    }
}
