<?php

namespace Tests\Unit;

use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ExportContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい検索条件はバリデーションを通過する()
    {
        $category = Category::factory()->create();

        $request = new ExportContactRequest;

        $data = [
            'keyword' => '山田',
            'gender' => 2,
            'category_id' => $category->id,
            'date' => '2024-01-01',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 不正な性別値は拒否される()
    {
        $request = new ExportContactRequest;

        $data = [
            'gender' => 999, // in:0,1,2,3 に含まれない
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function 存在しないカテゴリ_i_dは拒否される()
    {
        $request = new ExportContactRequest;

        $data = [
            'category_id' => 999999, // categories に存在しない
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function 不正な日付形式は拒否される()
    {
        $request = new ExportContactRequest;

        $data = [
            'date' => '2024-99-99', // 不正な日付
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function キーワードが255文字を超える場合は拒否される()
    {
        $request = new ExportContactRequest;

        $data = [
            'keyword' => str_repeat('あ', 256),
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }
}
