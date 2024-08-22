<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    protected $user;

    /**
     * @test
     *
     * @return void
     */
    public function ログアウトが成功すること(): void
    {
        $user = User::factory()->create();
        $this->withHeader('Referer', 'http://localhost');
        // actingAsでログイン状態にする
        $response = $this->actingAs($user)->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'ログアウトしました!',
            ]);
        // ユーザーが認証されていないこと
        $this->assertGuest('web');
    }

    /**
     * @test
     *
     * @return void
     */
    public function 認証が済んでいないユーザーがログアウトした場合はエラーが発生すること(): void
    {
        // メール認証が済んでいないユーザーを作成
        $this->user = User::factory()->create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password1',
            'email_verified_at' => null,
        ]);
        $this->withHeader('Referer', 'http://localhost');
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
