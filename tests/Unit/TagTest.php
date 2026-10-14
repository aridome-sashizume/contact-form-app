<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグは複数のお問い合わせに紐づく()
    {
        $tag = Tag::factory()->create();

        $category = Category::factory()->create();

        $contactA = Contact::factory()->create(
            [
                'category_id' => $category->id,
            ]
        );
        $contactB = Contact::factory()->create(
            [
                'category_id' => $category->id,
            ]
        );

        $tag->contacts()->attach([$contactA->id, $contactB->id]);

        $this->assertCount(2, $tag->contacts);
        $this->assertTrue($tag->contacts->contains($contactA));
        $this->assertTrue($tag->contacts->contains($contactB));
    }
}
