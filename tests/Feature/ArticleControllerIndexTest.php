<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ArticleControllerIndexTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Tag::factory()
            ->count(20)
            ->sequence(
                ['name' => '犬', 'key' => 'dog'],
                ['name' => '猫', 'key' => 'cat'],
                ['name' => 'ゲーム', 'key' => 'game'],
                ['name' => 'アニメ', 'key' => 'anime'],
                ['name' => 'レシピ', 'key' => 'recipe'],
                ['name' => 'サッカー', 'key' => 'soccer'],
                ['name' => '旅行', 'key' => 'travel'],
                ['name' => '仕事', 'key' => 'work'],
                ['name' => 'ランチ', 'key' => 'lunch'],
                ['name' => 'ヨガ', 'key' => 'yoga'],
                ['name' => 'ジム', 'key' => 'gym'],
                ['name' => 'コーヒー', 'key' => 'coffee'],
                ['name' => '映画', 'key' => 'movie'],
                ['name' => 'バスケ', 'key' => 'basketball'],
                ['name' => '野球', 'key' => 'baseball'],
                ['name' => 'オリンピック', 'key' => 'olympic'],
                ['name' => '東京', 'key' => 'tokyo'],
                ['name' => 'ペット', 'key' => 'pet'],
                ['name' => '車', 'key' => 'car'],
                ['name' => 'ビール', 'key' => 'beer'],
            )
            ->create();
        User::factory()->count(20)->create();
    }

    /**
     * @test
     *
     * @return void
     */
    public function 記事一覧が取得できること(): void
    {
        $tags = Tag::all();
        $articles = Article::factory()
            ->count(50)
            ->create()
            ->each(function ($article) use ($tags) {
                $article->tags()->attach(
                    $tags->random(random_int(1, 3))
                );
            });
        // id1から10まで取得する
        $articleLists = $articles->where('id', '<', 11);

        $response = $this->getJson('/api/articles');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'title',
                        'created_at',
                        'display_name',
                        'icon_path',
                        'number_of_likes',
                        'tags' => [
                            '*' => [
                                'name',
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJson(
                function (AssertableJson $json) use ($articleLists) {
                    foreach ($articleLists as $index => $articleList) {
                        $json->where('data.' . $index . '.title', $articleList->title)
                            ->where('data.' . $index . '.created_at', $articleList->created_at->toDateTimeString())
                            ->where('data.' . $index . '.display_name', $articleList->user->display_name)
                            ->where('data.' . $index . '.icon_path', $articleList->user->icon_path)
                            ->where('data.' . $index . '.number_of_likes', $articleList->userLikes->count())
                            ->has('data.' . $index . '.tags', function (AssertableJson $json) use ($articleList) {
                                foreach ($articleList->tags as $key => $tag) {
                                    $json->where($key . '.name', $tag->name);
                                }
                            })
                            ->etc();
                    }
                }
            )
            ->assertJsonCount(10, 'data');
    }

    /**
     * @test
     *
     * @return void
     */
    public function 論理削除された記事が含まれていないこと(): void
    {
        $tags = Tag::all();
        // articleのid番号51~60まで作成
        $articles = Article::factory()
            ->count(10)
            ->create()
            ->each(function ($article) use ($tags) {
                $article->tags()->attach(
                    $tags->random(random_int(1, 3))
                );
            });
        // articleのid番号51を論理削除する
        $articles->first()->delete();
        // 論理削除含まないid52番から60番まで取得
        $articleLists = Article::all();

        $response = $this->getJson('/api/articles');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'title',
                        'created_at',
                        'display_name',
                        'icon_path',
                        'number_of_likes',
                        'tags' => [
                            '*' => [
                                'name',
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJson(
                function (AssertableJson $json) use ($articleLists) {
                    foreach ($articleLists as $index => $articleList) {
                        $json->where('data.' . $index . '.title', $articleList->title)
                            ->where('data.' . $index . '.created_at', $articleList->created_at->toDateTimeString())
                            ->where('data.' . $index . '.display_name', $articleList->user->display_name)
                            ->where('data.' . $index . '.icon_path', $articleList->user->icon_path)
                            ->where('data.' . $index . '.number_of_likes', $articleList->userLikes->count())
                            ->has('data.' . $index . '.tags', function (AssertableJson $json) use ($articleList) {
                                foreach ($articleList->tags as $key => $tag) {
                                    $json->where($key . '.name', $tag->name);
                                }
                            })
                            ->etc();
                    }
                }
            )
            ->assertJsonCount(9, 'data');
    }

    /**
     * @test
     *
     * @return void
     */
    public function ページネーションできているか確認する(): void
    {
        $tags = Tag::all();
        $articles = Article::factory()
            ->count(20)
            ->create()
            ->each(function ($article) use ($tags) {
                $article->tags()->attach(
                    $tags->random(random_int(1, 3))
                );
            });
        // id番号71から80まで取得し、キーを並べ替え
        $articleLists = $articles->take(-10)->values();

        $response = $this->getJson('/api/articles?page=2');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'title',
                        'created_at',
                        'display_name',
                        'icon_path',
                        'number_of_likes',
                        'tags' => [
                            '*' => [
                                'name',
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJson(
                function (AssertableJson $json) use ($articleLists) {
                    foreach ($articleLists as $index => $articleList) {
                        $json->where('data.' . $index . '.title', $articleList->title)
                            ->where('data.' . $index . '.created_at', $articleList->created_at->toDateTimeString())
                            ->where('data.' . $index . '.display_name', $articleList->user->display_name)
                            ->where('data.' . $index . '.icon_path', $articleList->user->icon_path)
                            ->where('data.' . $index . '.number_of_likes', $articleList->userLikes->count())
                            ->has('data.' . $index . '.tags', function (AssertableJson $json) use ($articleList) {
                                foreach ($articleList->tags as $key => $tag) {
                                    $json->where($key . '.name', $tag->name);
                                }
                            })
                            ->etc();
                    }
                }
            )
            ->assertJsonCount(10, 'data');
    }
}
