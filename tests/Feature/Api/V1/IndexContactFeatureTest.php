<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // ContactFactory が category_id を生成できるように最低1件作成
        Category::factory()->create();
    }

    /** @test */
    public function 一覧が_jso_n形式で返る()
    {
        Contact::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
    }

    /** @test */
    public function キーワード検索が機能する()
    {
        Contact::factory()->create(['first_name' => '山田']);
        Contact::factory()->create(['first_name' => '佐藤']);

        $response = $this->getJson('/api/v1/contacts?keyword=山田');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function ページネーションが機能する()
    {
        Contact::factory()->count(30)->create();

        $response = $this->getJson('/api/v1/contacts?per_page=10');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page.0', 10);
    }

    /** @test */
    public function 不正な検索パラメータは422を返す()
    {
        $response = $this->getJson('/api/v1/contacts?date=2024/01/01');

        $response->assertStatus(422);
    }

    /** @test */
    public function 性別検索が機能する()
    {
        Contact::factory()->create(['gender' => 1]);
        Contact::factory()->create(['gender' => 2]);

        $response = $this->getJson('/api/v1/contacts?gender=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function カテゴリ検索が機能する()
    {
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();

        Contact::factory()->create(['category_id' => $cat1->id]);
        Contact::factory()->create(['category_id' => $cat2->id]);

        $response = $this->getJson("/api/v1/contacts?category_id={$cat1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function 日付検索が機能する()
    {
        Contact::factory()->create(['created_at' => '2024-01-01 10:00:00']);
        Contact::factory()->create(['created_at' => '2024-01-02 10:00:00']);

        $response = $this->getJson('/api/v1/contacts?date=2024-01-01');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function ページネーションのmeta情報が正しい()
    {
        Contact::factory()->count(30)->create();

        $response = $this->getJson('/api/v1/contacts?per_page=10');

        $response->assertStatus(200)
            ->assertJsonPath('meta.current_page.0', 1)
            ->assertJsonPath('meta.last_page.0', 3)
            ->assertJsonPath('meta.total.0', 30);

    }

    /** @test */
    public function 新しい順で返る()
    {
        $old = Contact::factory()->create(['created_at' => now()->subDay()]);
        $new = Contact::factory()->create(['created_at' => now()]);

        $response = $this->getJson('/api/v1/contacts');

        $this->assertEquals(
            $new->id,
            $response->json('data.0.id')
        );
    }
}
