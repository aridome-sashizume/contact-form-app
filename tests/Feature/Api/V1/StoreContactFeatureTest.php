<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正常にお問い合わせを作成できる()
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $payload = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '広島県広島市',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/v1/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.first_name', '山田')
            ->assertJsonPath('data.last_name', '太郎')
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonCount(2, 'data.tags');
    }

    /** @test */
    public function バリデーションエラー時は422が返る()
    {
        $response = $this->postJson('/api/v1/contacts', [
            'first_name' => '',
            'last_name' => '',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function メール形式が不正な場合は422が返る()
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/v1/contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'invalid-email',
            'tel' => '09012345678',
            'address' => '広島県広島市',
            'category_id' => $category->id,
            'detail' => 'テスト',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function buildingは任意項目で未入力でも作成できる()
    {
        $category = Category::factory()->create();

        $payload = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '広島県広島市',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
        ];

        $response = $this->postJson('/api/v1/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.building', null);
    }

    /** @test */
    public function タグが空配列の場合はタグが紐づかない()
    {
        $category = Category::factory()->create();

        $payload = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '広島県広島市',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [],
        ];

        $response = $this->postJson('/api/v1/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonCount(0, 'data.tags');
    }

    /** @test */
    public function 存在しないカテゴリ_i_dの場合は422が返る()
    {
        $response = $this->postJson('/api/v1/contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '広島県広島市',
            'category_id' => 999999,
            'detail' => 'お問い合わせ内容です。',
        ]);

        $response->assertStatus(422);
    }
}
