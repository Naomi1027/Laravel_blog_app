<?php

namespace Tests\Feature;

use App\Http\Requests\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
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
        $response->assertStatus(422)
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
     * @param bool $expect 期待値(true:バリデーションOK、false:バリデーションNG)
     *
     * @dataProvider invalidVerifyEmailDataProvider
     */
    public function リクエストボディに不備がある場合バリデーションのエラーメッセージがでること(
        array $keys,
        array $values,
        bool $expect
    ): void {
        //入力項目の配列（$keys）と値の配列($values)から、連想配列を生成する
        $dataList = array_combine($keys, $values);

        $request = new EmailVerificationRequest();
        //フォームリクエストで定義したルールを取得
        $rules = $request->rules();
        //Validatorファサードでバリデーターのインスタンスを取得、その際に入力情報とバリデーションルールを引数で渡す
        $validator = Validator::make($dataList, $rules);
        //入力情報がバリデーショルールを満たしている場合はtrue、満たしていな場合はfalseが返る
        $result = $validator->passes();
        //期待値($expect)と結果($result)を比較
        $this->assertEquals($expect, $result);
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
                false,
            ],
            'id形式エラー' => [
                ['id', 'hash'],
                ['test', sha1('test@example.com')],
                false,
            ],
            'hash必須エラー' => [
                ['id', 'hash'],
                [1, ''],
                false,
            ],
            'hash形式エラー' => [
                ['id', 'hash'],
                [1, 1234],
                false,
            ],
            'hash無効エラー' => [
                ['id', 'hash'],
                [1, 'invalid_hash'],
                false,
            ],
        ];
    }
}
