<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvExportContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未認証ユーザーは_cs_v出力できずログイン画面にリダイレクトされる()
    {
        $response = $this->get('/contacts/export');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン済み管理者は_cs_vをダウンロードできる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create(['content' => '商品のお届けについて']);

        Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'taro@example.com',
            'tel' => '090-1111-2222',
            'address' => '広島市',
            'building' => 'ビル101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容',
            'created_at' => now(),
        ]);

        $response = $this->get('/contacts/export');

        // 正常系：レスポンスが返る
        $response->assertStatus(200);

        // CSVヘッダーが正しい
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // ダウンロードファイル名が正しい
        $this->assertStringContainsString(
            'contacts_export_',
            $response->headers->get('Content-Disposition')
        );
    }

    /** @test */
    public function ログイン済み管理者はフィルタ条件付きで_cs_vを_d_lできる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $categoryA = Category::factory()->create(['content' => '商品のお届けについて']);
        $categoryB = Category::factory()->create(['content' => '返品について']);

        // フィルタに一致するデータ
        Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'hanako@example.com',
            'category_id' => $categoryA->id,
            'created_at' => now(),
        ]);

        // フィルタに一致しないデータ
        Contact::factory()->create([
            'first_name' => '佐藤',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'taro@example.com',
            'category_id' => $categoryB->id,
            'created_at' => now()->subDay(),
        ]);

        // keyword=山田, gender=2, category_id=A のフィルタでDL
        $response = $this->get('/contacts/export?keyword=山田&gender=2&category_id='.$categoryA->id);

        // DLできることを確認
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString(
            'contacts_export_',
            $response->headers->get('Content-Disposition')
        );
    }
}
