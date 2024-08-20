<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var User $user */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // テストユーザ作成
        $this->user = User::factory()->create();
    }

    /**
     * @test
     *
     * @return void
     */
    public function ログインが成功すること(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message', 'ログインに成功しました!')
                    ->where('user.id', $this->user->id)
                    ->etc()
            );
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * @dataProvider invalidLoginDataProvider
     *
     * @test
     *
     * @param array{ email: string, password: string } $data
     * @param string[] $expectedErrors
     * @return void
     */
    public function リクエストボディに不備がある場合バリデーションのエラーメッセージが発生すること(
        array $data,
        array $expectedErrors
    ): void {
        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(422)
            // バリデーションエラーがあることを確認
            ->assertJsonValidationErrors($expectedErrors);
        // ユーザーが認証されていないこと
        $this->assertGuest();
    }

    /**
     * @return array<mixed>
     */
    public static function invalidLoginDataProvider(): array
    {
        return [
            'emailが空の場合' => [
                [
                    'email' => '',
                    'password' => 'password',
                ],
                [
                    'email' => 'メールアドレスは必ず指定してください。',
                ],
            ],
            'emailが文字列でない場合' => [
                [
                    'email' => 123,
                    'password' => 'password',
                ],
                [
                    'email' => 'メールアドレスは文字列を指定してください。',
                ],
            ],
            'emailが不正な形式の場合' => [
                [
                    'email' => 'testexample',
                    'password' => 'password',
                ],
                [
                    'email' => 'メールアドレスには、有効なメールアドレスを指定してください。',
                ],
            ],
            'passwordが空の場合' => [
                [
                    'email' => 'test@example.com',
                    'password' => '',
                ],
                [
                    'password' => 'パスワードは必ず指定してください。',
                ],
            ],
            'passwordが文字列でない場合' => [
                [
                    'email' => 'test@example.com',
                    'password' => 123,
                ],
                [
                    'password' => 'パスワードは文字列を指定してください。',
                ],
            ],
        ];
    }

    /**
     * @test
     *
     * @return void
     */
    public function メール認証済でないとログインできないこと(): void
    {
        $this->user->email_verified_at = null;
        $this->user->save();

        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'ログインに失敗しました!',
            ]);
        $this->assertGuest();
    }
}
