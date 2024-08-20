<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // テストユーザ作成
        $this->user = User::factory()->create();
        $this->withHeader('Referer', 'http://localhost');
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
        // dd($response, $this->user);
        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message', 'ログインに成功しました!')
                    ->where('user.id', $this->user->id)
                    ->where('user.name', $this->user->name)
                    ->where('user.email', $this->user->email)
                    ->where('user.created_at', $this->user->created_at->toISOString())
                    ->where('user.updated_at', $this->user->updated_at->toISOString())
                    ->where('user.email_verified_at', $this->user->email_verified_at->toISOString())
                    ->where('user.icon_path', $this->user->icon_path)
                    ->where('user.display_name', $this->user->display_name)
            );
        // ユーザーが認証されていること
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
        // メール認証済でないユーザーを作成
        $this->user->email_verified_at = null;
        $this->user->save();

        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'メールアドレスが認証されていません!',
            ]);
        // ユーザーが認証されていないこと
        $this->assertGuest();
    }

    /**
     * @test
     *
     * @return void
     */
    public function 存在しないユーザーでログインしようとするとエラーが発生すること(): void
    {
        // 存在しないユーザーでログイン
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistUser@examplel.com',
            'password' => 'password',
        ]);
        $response->assertStatus(401)
            ->assertJson([
                'message' => '不正な認証情報です',
            ]);
        // ユーザーが認証されていないこと
        $this->assertGuest();
    }
}
