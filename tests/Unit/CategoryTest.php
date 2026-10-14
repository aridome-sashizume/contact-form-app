<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function カテゴリは複数のお問い合わせを持つ()
    {
        $category = Category::factory()->create();

        $contactA = Contact::factory()->create(['category_id' => $category->id]);
        $contactB = Contact::factory()->create(['category_id' => $category->id]);

        $this->assertCount(2, $category->contacts);
        $this->assertTrue($category->contacts->contains($contactA));
        $this->assertTrue($category->contacts->contains($contactB));
    }
}
