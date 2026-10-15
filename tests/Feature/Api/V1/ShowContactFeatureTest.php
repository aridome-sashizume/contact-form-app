<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 存在する_i_dの場合は詳細が_jso_n形式で返る()
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.category.id', $category->id);
    }

    /** @test */
    public function 詳細にタグ情報が含まれる()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $contact->tags()->createMany([
            ['name' => '重要'],
            ['name' => '至急'],
        ]);

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.tags');
    }

    /** @test */
    public function 存在しない_i_dの場合は404が返る()
    {
        $response = $this->getJson('/api/v1/contacts/999999');

        $response->assertStatus(404);
    }
}
