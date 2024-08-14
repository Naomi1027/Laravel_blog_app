<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
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
     * @dataProvider invalidRegisterDataProvider
     *
     * @test
     *
     * @param array{ name: string, email: string, password: string } $data
     * @param string[] $expectedErrors
     * @return void
     */
    public function リクエストボディに不備がある場合バリデーションのエラーメッセージが発生し、認証メールが送信されないこと(
        array $data,
        array $expectedErrors
    ): void {
        // Mailを偽装
        Notification::fake();
        Notification::assertNothingSent();

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
        // バリデーションエラーがあることを確認
            ->assertJsonValidationErrors($expectedErrors);

        // DBにユーザーが保存されていないことを確認
        $this->assertDatabaseCount('users', 0);

        // メールが送信されないことを確認
        Notification::assertNothingSent();
    }

    /**
     * @return array<mixed>
     */
    public static function invalidRegisterDataProvider(): array
    {
        return [
            '名前が空の場合' => [
                [
                    'name' => '',
                    'email' => 'test@example',
                    'password' => 'password',
                ],
                [
                    'name' => '名前は必須です。',
                ],
            ],
            '名前が文字列でない場合' => [
                [
                    'name' => 123,
                    'email' => 'test@example',
                    'password' => 'password',
                ],
                [
                    'name' => '名前は文字列にして下さい。',
                ],
            ],
            'メールアドレスが空の場合' => [
                [
                    'name' => 'test',
                    'email' => '',
                    'password' => 'password',
                ],
                [
                    'email' => 'メールアドレスは必須です。',
                ],
            ],
            'メールアドレスが文字列でない場合' => [
                [
                    'name' => 'test',
                    'email' => 123,
                    'password' => 'password',
                ],
                [
                    'email' => 'メールアドレスは文字列にして下さい。',
                ],
            ],
            'メールアドレスが不正な形式の場合' => [
                [
                    'name' => 'test',
                    'email' => 'testexample',
                    'password' => 'password',
                ],
                [
                    'email' => 'メールアドレスを入力して下さい。',
                ],
            ],
            'パスワードが空の場合' => [
                [
                    'name' => 'test',
                    'email' => 'test@example',
                    'password' => '',
                ],
                [
                    'password' => 'パスワードは必須です。',
                ],
            ],
            'パスワードが文字列でない場合' => [
                [
                    'name' => 'test',
                    'email' => 'test@example',
                    'password' => 123,
                ],
                [
                    'password' => 'パスワードは文字列にして下さい。',
                ],
            ],
        ];
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

        User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
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
