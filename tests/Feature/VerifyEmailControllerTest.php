<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerifyEmailControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function 新規登録したユーザーと認証メールが送られたユーザーが一致するか確認(): void
    {
        // Mailを偽装
        Notification::fake();
        // ユーザーを仮登録して認証メールを送信、email_verified_atがnullであることを確認
        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        event(new Registered($user));
        $this->assertDatabaseHas('users', [
            'email_verified_at' => null,
        ]);
        // メールが送信されたか確認
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

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
                'id' => $user->id,
                'hash' => sha1($user->email),
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
        // Mailを偽装
        Notification::fake();
        // ユーザーを仮登録して認証メールを送信、email_verified_atがnullであることを確認
        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        event(new Registered($user));
        $this->assertDatabaseHas('users', [
            'email_verified_at' => null,
        ]);
        User::first();
        // メールが送信されたか確認
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        // 別のユーザーを仮登録してemail_verified_atがnullであることを確認
        $anotherUser = User::create([
            'name' => 'anotherTest',
            'email' => 'anotherTest@example.com',
            'password' => 'password1',
        ]);
        $this->assertDatabaseHas('users', [
            'email_verified_at' => null,
        ]);

        // 別のユーザーがメール認証した場合
        $response = $this->postJson('api/email/verify', [
            'id' => $anotherUser->id,
            'hash' => sha1($user->email),
        ]);

        // 422レスポンスが返ってくること
        $response->assertStatus(422)
            // hashがバリデーションエラーになること
            ->assertInValid('hash')
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
}
