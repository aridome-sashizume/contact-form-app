<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しい検索条件はバリデーションを通過する
     */
    public function test_正しい検索条件はバリデーションを通過する()
    {
        Category::factory()->create();

        $request = new IndexContactRequest;

        $data = [
            'keyword' => 'テスト',
            'gender' => 1,
            'category_id' => 1,
            'date' => '2024-01-01',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /**
     * 不正な性別は拒否される
     */
    public function test_不正な性別は拒否される()
    {
        $request = new IndexContactRequest;

        $data = ['gender' => 999];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 存在しないカテゴリIDは拒否される
     */
    public function test_存在しないカテゴリ_i_dは拒否される()
    {
        $request = new IndexContactRequest;

        $data = ['category_id' => 999999];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }
}
