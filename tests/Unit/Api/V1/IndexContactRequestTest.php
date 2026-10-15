<?php

namespace Tests\Unit\Api\V1;

use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 検索条件が一部だけでもバリデーションを通過する()
    {
        $request = new IndexContactRequest;
        $category = Category::factory()->create();

        // keyword だけ
        $validator1 = Validator::make(['keyword' => '山田'], $request->rules());
        $this->assertTrue($validator1->passes());

        // gender だけ
        $validator2 = Validator::make(['gender' => 1], $request->rules());
        $this->assertTrue($validator2->passes());

        // category_id だけ
        $validator3 = Validator::make(['category_id' => $category->id], $request->rules());
        $this->assertTrue($validator3->passes());

        // date だけ
        $validator4 = Validator::make(['date' => '2024-01-01'], $request->rules());
        $this->assertTrue($validator4->passes());

        // per_page だけ
        $validator5 = Validator::make(['per_page' => 10], $request->rules());
        $this->assertTrue($validator5->passes());
    }

    /** @test */
    public function 不正な性別は拒否される()
    {
        $request = new IndexContactRequest;

        $data = ['gender' => 999];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正なカテゴリ_i_dは拒否される()
    {
        $request = new IndexContactRequest;

        $data = ['category_id' => 'abc'];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正な日付形式は拒否される()
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(['date' => '2024/01/01'], $request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正なper_pageは拒否される()
    {
        $category = Category::factory()->create();
        $request = new IndexContactRequest;

        $data = ['category_id' => $category->id, 'per_page' => 'abc']; // 数字以外はNG

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
    }
}
