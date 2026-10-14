<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しいタグ名はバリデーションを通過する
     */
    public function test_正しいタグ名はバリデーションを通過する()
    {
        $request = new StoreTagRequest;

        $data = ['name' => '有効なタグ名'];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /**
     * タグ名が空の場合は拒否される
     */
    public function test_タグ名が空の場合は拒否される()
    {
        $request = new StoreTagRequest;

        $data = ['name' => ''];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * タグ名が50文字を超える場合は拒否される
     */
    public function test_タグ名が50文字を超える場合は拒否される()
    {
        $request = new StoreTagRequest;

        $data = ['name' => str_repeat('あ', 51)];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 既に使用されているタグ名は新規登録時に拒否される
     */
    public function test_既に使用されているタグ名は新規登録時に拒否される()
    {
        Tag::factory()->create(['name' => '重複タグ']);

        $request = new StoreTagRequest;

        $data = ['name' => '重複タグ'];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }
}
