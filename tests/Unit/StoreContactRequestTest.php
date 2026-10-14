<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しい入力はバリデーションを通過する
     */
    public function test_正しい入力はバリデーションを通過する()
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $request = new StoreContactRequest;

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /**
     * 必須項目が欠けている場合はバリデーションエラーになる
     */
    public function test_必須項目が欠けている場合はバリデーションエラーになる()
    {
        $request = new StoreContactRequest;

        $data = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'gender' => '',
            'category_id' => '',
            'detail' => '',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 不正な電話番号形式は拒否される
     */
    public function test_不正な電話番号形式は拒否される()
    {
        $request = new StoreContactRequest;

        $data = ['tel' => 'abcde'];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 不正なメール形式は拒否される
     */
    public function test_不正なメール形式は拒否される()
    {
        $request = new StoreContactRequest;

        $data = ['email' => 'invalid-email'];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 不正な性別値は拒否される
     */
    public function test_不正な性別値は拒否される()
    {
        $request = new StoreContactRequest;

        $data = ['gender' => 999];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 存在しないカテゴリIDは拒否される
     */
    public function test_存在しないカテゴリ_i_dは拒否される()
    {
        $request = new StoreContactRequest;

        $data = ['category_id' => 999999];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 存在しないタグIDは拒否される
     */
    public function test_存在しないタグ_i_dは拒否される()
    {
        $category = Category::factory()->create();

        $request = new StoreContactRequest;

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'gender' => 1,
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容',
            'tag_ids' => [999999],
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }
}
