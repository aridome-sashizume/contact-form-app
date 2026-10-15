<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正常にお問い合わせを更新できる()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $payload = [
            'first_name' => '更新',
            'last_name' => '太郎',
            'gender' => 2,
            'email' => 'update@example.com',
            'tel' => '08099999999',
            'address' => '広島県福山市',
            'building' => '更新ビル202',
            'category_id' => $category->id,
            'detail' => '更新後の内容です。',
            'tag_ids' => [], // 空配列でもOK（タグ解除）
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.first_name', '更新')
            ->assertJsonPath('data.last_name', '太郎')
            ->assertJsonPath('data.email', 'update@example.com')
            ->assertJsonPath('data.category.id', $category->id);
    }

    /** @test */
    public function 存在しない_i_dの場合は404が返る()
    {
        $payload = [
            'first_name' => '更新',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09011112222',
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'テスト',
        ];

        $response = $this->putJson('/api/v1/contacts/999999', $payload);

        $response->assertStatus(404);
    }

    /** @test */
    public function バリデーションエラー時は422が返る()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        // first_name と last_name が必須なので空で送る
        $response = $this->putJson("/api/v1/contacts/{$contact->id}", [
            'first_name' => '',
            'last_name' => '',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function タグが更新される()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // 既存タグを2件作成
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        // 初期状態でタグを紐づけ
        $contact->tags()->sync([$tag1->id]);

        // 更新時に tag2 のみに変更
        $payload = [
            'first_name' => '更新',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'update@example.com',
            'tel' => '09011112222',
            'address' => '広島県広島市',
            'category_id' => $category->id,
            'detail' => '更新内容',
            'tag_ids' => [$tag2->id],
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.tags')
            ->assertJsonPath('data.tags.0.id', $tag2->id);
    }
}
