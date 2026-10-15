<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーは管理画面を表示できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin.index');
    }

    /** @test */
    public function 未認証ユーザーは管理画面にアクセスできずログインへリダイレクトされる()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function 管理画面で検索フィルタが全て機能する()
    {
        $user = User::factory()->create();

        // カテゴリ2つ
        $categoryA = Category::factory()->create(['content' => '商品のお問い合わせ']);
        $categoryB = Category::factory()->create(['content' => '商品の交換について']);

        // 条件に一致するレコード
        $match = Contact::factory()->create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'category_id' => $categoryA->id,
            'created_at' => '2024-01-01',
        ]);

        // 条件に一致しないレコード（除外されるべき）
        Contact::factory()->create([
            'first_name' => '花子',
            'last_name' => '佐藤',
            'gender' => 2,
            'category_id' => $categoryB->id,
            'created_at' => '2024-02-01',
        ]);

        $response = $this->actingAs($user)->get(
            '/admin?keyword=山田&gender=1&category_id='.$categoryA->id.'&date=2024-01-01'
        );

        $response->assertStatus(200);

        // 一致するレコードは表示される
        $response->assertSee('山田');
        $response->assertSee('太郎'); // ← keyword の first_name も確認
        $response->assertSee('商品のお問い合わせ'); // ← categoryA のカテゴリ名も確認

        // 一致しないレコードは表示されない
        $response->assertDontSee('佐藤');
        $response->assertDontSee('花子');
    }

    /** @test */
    public function keyword単独検索が機能する()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'category_id' => $category->id,
        ]);

        Contact::factory()->create([
            'first_name' => '花子',
            'last_name' => '佐藤',
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get('/admin?keyword=山田');

        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    /** @test */
    public function gender単独検索が機能する()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->create([
            'first_name' => '太郎',
            'gender' => 1,
            'category_id' => $category->id,
        ]);

        Contact::factory()->create([
            'first_name' => '花子',
            'gender' => 2,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get('/admin?gender=1');

        $response->assertSee('太郎');
        $response->assertDontSee('花子');
    }

    /** @test */
    public function category単独検索が機能する()
    {
        $user = User::factory()->create();

        $categoryA = Category::factory()->create(['content' => '商品のお問い合わせ']);
        $categoryB = Category::factory()->create(['content' => '商品の交換について']);

        Contact::factory()->create(['category_id' => $categoryA->id]);
        Contact::factory()->create(['category_id' => $categoryB->id]);

        $response = $this->actingAs($user)->get('/admin?category_id='.$categoryA->id);

        $response->assertSee('商品のお問い合わせ');
    }

    /** @test */
    public function 管理画面は7件ごとにページネーションされる()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();
        Contact::factory()->count(10)->create(
            [
                'category_id' => $category->id,
            ]
        );

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $contacts = $response->viewData('contacts');
        $this->assertEquals(7, $contacts->perPage());
    }

    /** @test */
    public function date単独検索が機能する()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Contact::factory()->create([
            'first_name' => '太郎',
            'created_at' => '2024-01-01',
            'category_id' => $category->id,
        ]);

        Contact::factory()->create([
            'first_name' => '花子',
            'created_at' => '2024-02-01',
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get('/admin?date=2024-01-01');

        $response->assertSee('太郎');
        $response->assertDontSee('花子');
    }

    /** @test */
    public function 管理画面でお問い合わせ詳細がカテゴリ情報付きで表示される()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['content' => '商品のお届けについて']);
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get('/admin/contacts/'.$contact->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertSee($contact->first_name);
        $response->assertSee('商品のお届けについて');
    }

    /** @test */
    public function 管理画面でお問い合わせを削除できる()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(
            [
                'category_id' => $category->id,
            ]
        );

        $response = $this->actingAs($user)->delete('/admin/contacts/'.$contact->id);

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
