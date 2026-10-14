<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせはカテゴリに属する()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $this->assertEquals($category->id, $contact->category->id);
    }

    /** @test */
    public function お問い合わせは複数のタグと同期できる()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(
            [
                'category_id' => $category->id,
            ]
        );
        $tagA = Tag::factory()->create();
        $tagB = Tag::factory()->create();

        $contact->tags()->sync([$tagA->id, $tagB->id]);

        $this->assertCount(2, $contact->tags);
        $this->assertTrue($contact->tags->contains($tagA));
        $this->assertTrue($contact->tags->contains($tagB));
    }
}
