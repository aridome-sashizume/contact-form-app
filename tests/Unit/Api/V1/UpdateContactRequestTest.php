<?php

namespace Tests\Unit\Api\V1;

use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全ての有効な入力値でバリデーションを通過する()
    {
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $request = new UpdateContactRequest;

        $data = [
            'first_name' => '更新',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'update@example.com',
            'tel' => '09011112222',
            'address' => '広島市',
            'building' => '202',
            'category_id' => $category->id,
            'detail' => '更新内容',
            'tag_ids' => [$tag1->id, $tag2->id],
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function buildingがなくてもバリデーションを通過する()
    {
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $request = new UpdateContactRequest;

        $data = [
            'first_name' => '更新',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'update@example.com',
            'tel' => '09011112222',
            'address' => '広島市',
            // building なし
            'category_id' => $category->id,
            'detail' => '更新内容',
            'tag_ids' => [$tag1->id, $tag2->id],
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 必須項目が欠落するとバリデーションは失敗する()
    {
        $request = new UpdateContactRequest;

        $validator = Validator::make([], $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正な性別は拒否される()
    {
        $request = new UpdateContactRequest;

        $validator = Validator::make(['gender' => 999], $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正なメール形式は拒否される()
    {
        $request = new UpdateContactRequest;

        $validator = Validator::make(['email' => 'invalid-email'], $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正な電話番号は拒否される()
    {
        $request = new UpdateContactRequest;

        $validator = Validator::make(['tel' => 'abcde'], $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正なカテゴリ_i_dは拒否される()
    {
        $request = new UpdateContactRequest;

        $validator = Validator::make(['category_id' => 'abc'], $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function tag_idsが配列でない場合は拒否される()
    {
        $request = new UpdateContactRequest;

        $validator = Validator::make(['tag_ids' => '1,2'], $request->rules());
        $this->assertTrue($validator->fails());
    }
}
