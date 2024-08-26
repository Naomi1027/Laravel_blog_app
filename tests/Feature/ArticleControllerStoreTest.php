<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class ArticleControllerStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    protected User $user;

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

        $this->user = User::factory()->create();

        $this->withHeader('Referer', 'http://localhost');
    }

    /**
     * @test
     *
     * @return void
     */
    public function 記事投稿ができること(): void
    {
        $tags = Tag::take(3)->get();

        // actingAsでログイン状態にする
        $this->actingAs($this->user);

        $response = $this->postJson('/api/articles', [
            'title' => 'test title',
            'content' => 'test content',
            'tags' => [
                $tags[0]->id,
                $tags[1]->id,
                $tags[2]->id,
            ],
        ]);

        $response->assertStatus(201)
            // レスポンスの構造を確認
            ->assertJsonStructure([
                'data' => [
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
            ])
            // レスポンスの内容を確認
            ->assertJson([
                'data' => [
                    'title' => 'test title',
                    'display_name' => $this->user->display_name,
                    'icon_path' => $this->user->icon_path,
                    'number_of_likes' => 0,
                    'tags' => [
                        ['name' => $tags[0]->name],
                        ['name' => $tags[1]->name],
                        ['name' => $tags[2]->name],
                    ],
                ],
            ]);
        // DBに保存されていることを確認
        $this->assertDatabaseHas('articles', [
            'title' => 'test title',
            'content' => 'test content',
            'user_id' => $this->user->id,
        ]);
        // 中間テーブルに保存されていることを確認
        $this->assertDatabaseHas('article_tag', [
            'article_id' => Article::first()->id,
            'tag_id' => $tags[0]->id,
        ]);
        $this->assertDatabaseHas('article_tag', [
            'article_id' => Article::first()->id,
            'tag_id' => $tags[1]->id,
        ]);
        $this->assertDatabaseHas('article_tag', [
            'article_id' => Article::first()->id,
            'tag_id' => $tags[2]->id,
        ]);
    }

    /**
     * @dataProvider invalidLoginDataProvider
     *
     * @test
     *
     * @param array{ title: string, content: string, tags: array<int> } $data
     * @param string[] $expectedErrors
     * @return void
     */
    public function リクエストボディに不備がある場合バリデーションのエラーメッセージが発生すること(
        array $data,
        array $expectedErrors
    ): void {
        $this->actingAs($this->user);
        $response = $this->postJson('/api/articles', $data);

        $response->assertStatus(422)

            // エラーメッセージの内容を確認
            ->assertJsonValidationErrors($expectedErrors);

        // DBに保存されていないことを確認
        $this->assertDatabaseCount('articles', 0);
    }

    /**
     * @return array<mixed>
     */
    public static function invalidLoginDataProvider(): array
    {
        return [
            'titleが空の場合' => [
                [
                    'title' => '',
                    'content' => 'test content',
                    'tags' => [1, 2, 3],
                ],
                [
                    'title' => 'タイトルは必須です。',
                ],
            ],
            'titleが文字列でない場合' => [
                [
                    'title' => 123,
                    'content' => 'test content',
                    'tags' => [1, 2, 3],
                ],
                [
                    'title' => 'タイトルは文字列にして下さい。',
                ],
            ],
            'titleが255文字以上の場合' => [
                [
                    'title' => str_repeat('あ', 256),
                    'content' => 'test content',
                    'tags' => [1, 2, 3],
                ],
                [
                    'title' => 'タイトルは255文字以内でお願いします。',
                ],
            ],
            'contentが空の場合' => [
                [
                    'title' => 'test title',
                    'content' => '',
                    'tags' => [1, 2, 3],
                ],
                [
                    'content' => '本文は必須です。',
                ],
            ],
            'contentが文字列でない場合' => [
                [
                    'title' => 'test title',
                    'content' => 123,
                    'tags' => [1, 2, 3],
                ],
                [
                    'content' => '本文は文字列にして下さい。',
                ],
            ],
            'tagsが3つ以上の場合' => [
                [
                    'title' => 'test title',
                    'content' => 'test content',
                    'tags' => [1, 2, 3, 4],
                ],
                [
                    'tags' => 'タグは3つまで選択して下さい',
                ],
            ],
            'tagsが文字列の場合' => [
                [
                    'title' => 'test title',
                    'content' => 'test content',
                    'tags' => 'test',
                ],
                [
                    'tags' => 'tagsは配列でなくてはなりません。',
                ],
            ],
            'tagsの要素が文字列の場合' => [
                [
                    'title' => 'test title',
                    'content' => 'test content',
                    'tags' => ['test', 'test', 'test'],
                ],
                [
                    'tags.0' => '選択されたタグが不正です。',
                    'tags.1' => '選択されたタグが不正です。',
                    'tags.2' => '選択されたタグが不正です。',
                ],
            ],
            'tagsが存在しないIDの場合' => [
                [
                    'title' => 'test title',
                    'content' => 'test content',
                    'tags' => [100, 101, 102],
                ],
                [
                    'tags.0' => '選択されたタグは存在しません。',
                    'tags.1' => '選択されたタグは存在しません。',
                    'tags.2' => '選択されたタグは存在しません。',
                ],
            ],
        ];
    }

    /**
     * @test
     *
     * @return void
     */
    public function 未ログインでは記事投稿ができないこと(): void
    {
        $tags = Tag::all();

        $response = $this->postJson('/api/articles', [
            'title' => 'test title',
            'content' => 'test content',
            'tags' => [
                $tags[0]->id,
                $tags[1]->id,
                $tags[2]->id,
            ],
        ]);

        $response->assertStatus(401)
            ->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED);

        // DBに保存されていないことを確認
        $this->assertDatabaseCount('articles', 0);
    }
}
