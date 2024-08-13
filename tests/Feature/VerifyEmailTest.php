<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
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
        $this->postJson('/api/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $this->assertDatabaseHas('users', [
            'email_verified_at' => null,
        ]);
        $user = User::first();
        // メールが送信されたか確認
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        $response = $this->postJson('api/email/verify/{id}/{hash}', [
            'id' => $user->id,
            'hash' => $user->password,
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
                'hash' => $user->password,
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
        $this->postJson('/api/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $this->assertDatabaseHas('users', [
            'email_verified_at' => null,
        ]);
        $user = User::first();
        // メールが送信されたか確認
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        // 別のユーザーを仮登録してemail_verified_atがnullであることを確認
        $anotherUser = User::create([
            'name' => 'anotherTest',
            'email' => 'anothertest@example.com',
            'password' => 'password1',
        ]);
        $this->assertDatabaseHas('users', [
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('api/email/verify/{id}/{hash}', [
            'id' => $user->id,
            'hash' => $user->password,
        ]);

        $response->assertStatus(200)
            // バリデーションエラーがないこと
            ->assertValid([
                'id',
                'hash',
            ])
            // 違うユーザーのidでは認証されていないこと
            ->assertJsonMissing([
                'id' => $anotherUser->id,
                'hash' => $anotherUser->password,
            ]);
        //違うユーザーのemail_verified_atがnullであること
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => Carbon::now(),
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $anotherUser->id,
            'email_verified_at' => null,
        ]);
    }
}
