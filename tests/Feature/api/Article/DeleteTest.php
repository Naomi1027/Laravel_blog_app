<?php

namespace Tests\Feature\Api\Article;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    protected User $user;

    /** @var Article */
    protected Article $article;

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

        // 記事を作成
        $this->article = Article::factory()->create([
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
        ]);

        $this->withHeader('Referer', 'http://localhost');
    }

    /**
     * @test
     */
    public function 記事の削除ができること(): void
    {
        // 削除前のデータがDBに存在することを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);

        $tags = Tag::take(3)->get();
        // 記事にタグを紐づける
        $this->article->tags()->attach($tags->pluck('id'));
        // 削除前にarticle_tagテーブルにデータが存在することを確認
        $this->assertDatabaseHas('article_tag', [
            'article_id' => $this->article->id,
            'tag_id' => $tags[0]->id,
        ]);
        $this->assertDatabaseHas('article_tag', [
            'article_id' => $this->article->id,
            'tag_id' => $tags[1]->id,
        ]);
        $this->assertDatabaseHas('article_tag', [
            'article_id' => $this->article->id,
            'tag_id' => $tags[2]->id,
        ]);

        // ログインして記事を削除
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/articles/'. $this->article->id);
        // 時間を固定
        Carbon::setTestNow(Carbon::parse($response['detail']['deleted_at']));
        // 200レスポンスが返ることを確認
        $response->assertStatus(200)
            ->assertJson([
                'message' => '記事を削除しました。',
                'detail' => [
                    'id' => $this->article->id,
                    'title' => 'タイトル',
                    'content' => '本文',
                    'created_at' => $this->article->created_at->toISOString(),
                    'updated_at' => $this->article->updated_at->toISOString(),
                    'deleted_at' => now()->toISOString(),
                    'user_id' => $this->user->id,
                ],
                'statusCode' => 200,
            ]);
        // 論理削除されたことを確認
        $this->assertSoftDeleted($this->article);
        // 削除後のarticlesテーブルを確認
        $this->assertDatabaseHas('articles', [
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);
        // 削除後のarticle_tagテーブルにデータがないことを確認
        $this->assertDatabaseMissing('article_tag', [
            'article_id' => $this->article->id,
            'tag_id' => $tags[0]->id,
        ]);
        $this->assertDatabaseMissing('article_tag', [
            'article_id' => $this->article->id,
            'tag_id' => $tags[1]->id,
        ]);
        $this->assertDatabaseMissing('article_tag', [
            'article_id' => $this->article->id,
            'tag_id' => $tags[2]->id,
        ]);
    }

    /**
     * @test
     */
    public function 別のユーザーの記事は削除できず403エラーが返ること(): void
    {
        // データがDBに存在することを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);

        // 別ユーザーを作成
        $anotherUser = User::factory()->create();
        // 別のユーザーで記事を削除
        $response = $this->actingAs($anotherUser)
            ->deleteJson('/api/articles/'. $this->article->id);
        // 403エラーが返ることを確認
        $response->assertStatus(403);
        // 削除後のデータがDBに存在することを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * @test
     */
    public function ログインしていない場合は記事を削除できず401エラーが返ること(): void
    {
        // データがDBに存在することを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);
        // ログインしていない状態で記事を削除
        $response = $this->deleteJson('/api/articles/'. $this->article->id);
        // 401エラーが返ることを確認
        $response->assertStatus(401);
        // データが削除されていないことを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * @test
     */
    public function 記事削除時に記事が存在しない場合は404エラーが返ること(): void
    {
        // データがDBに存在することを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);
        // ログインして存在しない記事IDを指定して記事を削除
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/articles/'. ($this->article->id + 1));
        // 404レスポンスが返ってくること
        $response->assertStatus(404);
        // データが削除されていないことを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * @test
     */
    public function 削除された記事を削除しようとすると404エラーが返ること(): void
    {
        // データがDBに存在することを確認
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => null,
        ]);
        // ログインして記事を削除
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/articles/'. $this->article->id);
        // 204レスポンスが返ることを確認
        $response->assertStatus(200);
        // 論理削除されたことを確認
        $this->assertSoftDeleted($this->article);
        // 削除後のarticlesテーブルを確認
        $this->assertDatabaseHas('articles', [
            'title' => 'タイトル',
            'content' => '本文',
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);
        // 削除後に削除しようとすると404エラーが返ることを確認
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/articles/'. $this->article->id);
        $response->assertStatus(404);
    }
}
