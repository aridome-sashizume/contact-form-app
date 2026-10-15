<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインページが表示される()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('メールアドレス');
        $response->assertSee('パスワード');
    }

    /** @test */
    public function ログイン時にすべてが未入力の場合はエラーになる()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function ログイン時にメールが未入力の場合はエラーになる()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function ログイン時にパスワードが未入力の場合はエラーになる()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function 認証失敗時はエラーになる()
    {
        $response = $this->post('/login', [
            'email' => 'not-exist@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function 認証成功時は管理画面にリダイレクトされる()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertRedirect('/admin');
    }
}
