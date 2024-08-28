<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerUpdateTest extends TestCase
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
    public function 記事編集ができること(): void
    {
        $tags = Tag::take(3)->get();
        // 記事を投稿
        $article = Article::factory()->create([
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // 編集前のDBの状態を確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson('/api/articles/'. $article->id, [
                'title' => 'タイトル編集',
                'content' => '本文編集',
                'tags' => [
                    $tags[0]->id,
                    $tags[1]->id,
                ],
            ]);
        // 200レスポンスが返ってくること
        $response->assertStatus(200)
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
                    'title' => 'タイトル編集',
                    'created_at' => $article->created_at,
                    'display_name' => $this->user->display_name,
                    'icon_path' => $this->user->icon_path,
                    'number_of_likes' => 0,
                    'tags' => [
                        ['name' => $tags[0]->name],
                        ['name' => $tags[1]->name],
                    ],
                ],
            ]);
        // 編集後DBに保存されていることを確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル編集',
            'content' => '本文編集',
            'user_id' => $this->user->id,
        ]);
        // 中間テーブルに保存されていることを確認
        $this->assertDatabaseHas('article_tag', [
            'article_id' => $article->id,
            'tag_id' => $tags[0]->id,
        ]);
        // 中間テーブルに保存されていることを確認
        $this->assertDatabaseHas('article_tag', [
            'article_id' => $article->id,
            'tag_id' => $tags[1]->id,
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
        // 記事を投稿
        $article = Article::factory()->create([
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // 編集前のDBの状態を確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // ログインして記事を編集
        $response = $this->actingAs($this->user)
            ->putJson('/api/articles/'. $article->id, $data);
        // 422レスポンスが返ってくること
        $response->assertStatus(422)
        // エラーメッセージを確認
            ->assertJsonValidationErrors($expectedErrors);
        // // DBに編集が反映されていないことを確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
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
    public function 未ログインでは記事編集ができないこと(): void
    {
        $tags = Tag::take(3)->get();
        // 記事を投稿
        $article = Article::factory()->create([
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // 編集前のDBの状態を確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // 未ログインで記事を編集
        $response = $this->putJson('/api/articles/'. $article->id, [
            'title' => 'タイトル編集',
            'content' => '本文編集',
            'tags' => [
                $tags[0]->id,
                $tags[1]->id,
            ],
        ]);
        // 401レスポンスが返ってくること
        $response->assertStatus(401);
        // DBに編集が反映されていないことを確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function 他のユーザーの記事を編集しようとした場合403エラーが返り編集できないこと(): void
    {
        $tags = Tag::take(3)->get();
        // 記事を投稿
        $article = Article::factory()->create([
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // 編集前のDBの状態を確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // 別のユーザーを作成
        $anotherUser = User::factory()->create();
        // 別のユーザーでログイン
        $response = $this->actingAs($anotherUser)
            ->putJson('/api/articles/'. $article->id, [
                'title' => 'タイトル編集',
                'content' => '本文編集',
                'tags' => [
                    $tags[0]->id,
                    $tags[1]->id,
                ],
            ]);
        // 403レスポンスが返ってくること
        $response->assertStatus(403);
        // DBに編集が反映されていないことを確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function 存在しない記事IDを指定した場合404エラーが返ること(): void
    {
        $tags = Tag::take(3)->get();
        // 記事を投稿
        $article = Article::factory()->create([
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // 編集前のDBの状態を確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
        // ログインして存在しない記事IDを指定して記事を編集
        $response = $this->actingAs($this->user)
            ->putJson('/api/articles/'. $article->id + 1, [
                'title' => 'タイトル編集',
                'content' => '本文編集',
                'tags' => [
                    $tags[0]->id,
                    $tags[1]->id,
                ],
            ]);
        // 404レスポンスが返ってくること
        $response->assertStatus(404);
        // DBに編集が反映されていないことを確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);
    }
}
