<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all();

        Article::factory()
            ->count(50)
            ->create()
            ->each(function ($article) use ($tags) {
                $article->tags()->attach(
                    $tags->random(random_int(1, 3))
                );
            });
    }
}
