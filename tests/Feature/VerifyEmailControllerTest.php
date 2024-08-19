<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyEmailControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'email_verified_at' => null,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function 新規登録したユーザーと認証メールが送られたユーザーが一致し認証されること(): void
    {
        $user = User::first();
        $response = $this->postJson('api/email/verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);
        // 200レスポンスが返ってきている
        $response->assertStatus(200)
            // バリデーションエラーがないこと
            ->assertValid([
                'id',
                'hash',
            ])
            // 新規登録したユーザーと認証メールが送られたユーザーが一致するか
            ->assertJson([
                'message' => 'Successfully Verified!',
            ]);
        // ユーザーが認証されたか確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function 違うユーザーがメール認証した場合は認証されないこと(): void
    {
        $user = User::first();
        // 別のユーザーを仮登録してemail_verified_atがnullであることを確認
        $anotherUser = User::factory()->create([
            'name' => 'anotherTest',
            'email' => 'anotherTest@example.com',
            'password' => 'password1',
            'email_verified_at' => null,
        ]);
        $this->assertDatabaseHas('users', [
            'email_verified_at' => null,
        ]);

        // 別のユーザーがメール認証した場合
        $response = $this->postJson('api/email/verify', [
            'id' => $anotherUser->id,
            'hash' => sha1($user->email),
        ]);
        // dd($response);

        // 422レスポンスが返ってくること
        $response->assertStatus(400)
            // hashがバリデーションエラーになること
            ->assertValid('errors')
            // 違うユーザーでは認証されていないこと
            ->assertJsonMissing([
                'id' => $anotherUser->id,
                'hash' => sha1($anotherUser->email),
            ]);
        //email_verified_atがnullであること
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => null,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $anotherUser->id,
            'email_verified_at' => null,
        ]);
    }

    /**
     * カスタムリクエストのバリデーションテスト
     *
     *@test
     *
     * @param array{ id: int, hash: string } $keys 項目名の配列
     * @param string[] $values 値の配列
     * @param string[] $expectedErrors
     *
     * @dataProvider invalidVerifyEmailDataProvider
     */
    public function リクエストボディに不備がある場合バリデーションのエラーメッセージがでること(
        array $keys,
        array $values,
        array $expectedErrors
    ): void {
        //入力項目の配列（$keys）と値の配列($values)から、連想配列を生成する
        $dataList = array_combine($keys, $values);

        $response = $this->postJson('api/email/verify', $dataList);
        $response->assertStatus(422)
        // バリデーションエラーがあることを確認
            ->assertJsonValidationErrors($expectedErrors);
    }

    /**
     * @return array<mixed>
     */
    public static function invalidVerifyEmailDataProvider(): array
    {
        return [
            'id必須エラー' => [
                ['id', 'hash'],
                ['', sha1('test@example.com')],
                [
                    'id' => 'idは必ず指定してください。',
                ],
            ],
            'id形式エラー' => [
                ['id', 'hash'],
                ['test', sha1('test@example.com')],
                [
                    'id' => 'idは整数で指定してください。',
                ],
            ],
            'hash必須エラー' => [
                ['id', 'hash'],
                [1, ''],
                [
                    'hash' => 'hashは必ず指定してください。',
                ],
            ],
            'hash形式エラー' => [
                ['id', 'hash'],
                [1, 1234],
                [
                    'hash' => 'hashは文字列を指定してください。',
                ],
            ],
            'hash無効エラー' => [
                ['id', 'hash'],
                [1, 'invalid_hash'],
                [
                    'id' => '選択されたidは正しくありません。',
                ],
            ],
        ];
    }
}
