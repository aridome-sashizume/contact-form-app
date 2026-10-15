<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーはタグ編集ページを表示できる()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '旧タグ名']);

        $response = $this->actingAs($user)->get('/admin/tags/'.$tag->id.'/edit');

        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.edit');
        $response->assertSee('旧タグ名');
    }

    /** @test */
    public function 認証済みユーザーはタグを作成できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/tags', [
            'name' => '新規タグ',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新規タグ']);
    }

    /** @test */
    public function 認証済みユーザーはタグを更新できる()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '旧タグ名']);

        $response = $this->actingAs($user)->put('/admin/tags/'.$tag->id, [
            'name' => '更新後タグ名',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '更新後タグ名']);
    }

    /** @test */
    public function 認証済みユーザーはタグを削除できる()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->delete('/admin/tags/'.$tag->id);

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /** @test */
    public function 未認証ユーザーはタグ編集ページにアクセスできない()
    {
        $tag = Tag::factory()->create();

        $response = $this->get('/admin/tags/'.$tag->id.'/edit');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function 未認証ユーザーはタグ作成更新削除ができない()
    {
        $tag = Tag::factory()->create();

        $this->post('/admin/tags', ['name' => '不正'])->assertRedirect('/login');
        $this->put('/admin/tags/'.$tag->id, ['name' => '不正'])->assertRedirect('/login');
        $this->delete('/admin/tags/'.$tag->id)->assertRedirect('/login');
    }
}
