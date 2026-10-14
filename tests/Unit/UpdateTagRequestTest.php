<?php

namespace Tests\Unit;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ルートパラメータに tag を返す簡易オブジェクト
     */
    private function routeWithTag($tag)
    {
        return new class($tag)
        {
            public function __construct(private $tag) {}

            public function parameter($name)
            {
                return $this->tag;
            }
        };
    }

    public function test_正しいタグ名はバリデーションを通過する()
    {
        $tag = Tag::factory()->create();

        $request = new UpdateTagRequest;
        $request->setContainer(app());
        $request->setRouteResolver(fn () => $this->routeWithTag($tag));

        $validator = Validator::make(['name' => '有効なタグ名'], $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_タグ名が空の場合は拒否される()
    {
        $tag = Tag::factory()->create();

        $request = new UpdateTagRequest;
        $request->setContainer(app());
        $request->setRouteResolver(fn () => $this->routeWithTag($tag));

        $validator = Validator::make(['name' => ''], $request->rules());
        $this->assertFalse($validator->passes());
    }

    public function test_タグ名が50文字を超える場合は拒否される()
    {
        $tag = Tag::factory()->create();

        $request = new UpdateTagRequest;
        $request->setContainer(app());
        $request->setRouteResolver(fn () => $this->routeWithTag($tag));

        $validator = Validator::make(['name' => str_repeat('あ', 51)], $request->rules());
        $this->assertFalse($validator->passes());
    }

    public function test_更新時に自分自身のタグ名は許可される()
    {
        $tag = Tag::factory()->create(['name' => '既存タグ']);

        $request = new UpdateTagRequest;
        $request->setContainer(app());
        $request->setRouteResolver(fn () => $this->routeWithTag($tag));

        $validator = Validator::make(['name' => '既存タグ'], $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_更新時に他のタグ名と重複する場合は拒否される()
    {
        $tagA = Tag::factory()->create(['name' => 'タグA']);
        $tagB = Tag::factory()->create(['name' => 'タグB']);

        $request = new UpdateTagRequest;
        $request->setContainer(app());
        $request->setRouteResolver(fn () => $this->routeWithTag($tagA));

        $validator = Validator::make(['name' => 'タグB'], $request->rules());
        $this->assertFalse($validator->passes());
    }
}
