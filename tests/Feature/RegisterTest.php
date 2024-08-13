<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function 新規登録ができていること(): void
    {
        // Mailを偽装
        Notification::fake();
        Notification::assertNothingSent();

        $response = $this->postJson('/api/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
        // バリデーションエラーがないことを確認
            ->assertValid([
                'name',
                'email',
                'password',
            ]);
        // DBにユーザーが保存されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);

        // メールが送信されたか確認
        Notification::assertSentTo(
            User::first(),
            VerifyEmail::class
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function リクエストボディに不備がある場合バリデーションのエラーメッセージが発生し、認証メールが送信されないこと(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        // レスポンスに未処理のエンティティ(422)HTTPステータスコードがあること
        $response->assertStatus(422)
        // バリデーションエラーがあることを確認
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
        // 認証メールが送信されないこと
        Notification::assertNothingSent();
    }

    /**
     * @test
     *
     * @return void
     */
    public function 登録済のユーザーは仮登録できないこと(): void
    {
        // Mailを偽装
        Notification::fake();
        Notification::assertNothingSent();

        User::factory()->create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            // 422レスポンスが返ってきているか
        ])->assertStatus(422)
        // 「メールアドレスの値は既に存在しています」のバリデーションエラーが出ること
            ->assertJsonValidationErrors([
                'email' => 'メールアドレスの値は既に存在しています',
            ]);
        // DBにユーザーが保存されないこと
        $this->assertDatabaseCount('users', 1);
        // メールが送信されないこと
        Notification::assertNotSentTo(
            User::first(),
            VerifyEmail::class
        );
    }
}
