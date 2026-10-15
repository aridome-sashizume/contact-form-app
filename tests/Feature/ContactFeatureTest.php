<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_入力ページが正常に表示されカテゴリとタグが渡される()
    {
        $category = Category::factory()->create(['content' => '商品のお届けについて']);
        $tag = Tag::factory()->create(['name' => '質問']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('contact.index');
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');

        $response->assertSee('商品のお届けについて');
        $response->assertSee('質問');
    }

    public function test_サンクスページが正常に表示される()
    {
        $response = $this->get('/thanks');

        $response->assertStatus(200);
        $response->assertViewIs('contact.thanks');
    }

    public function test_確認ページが表示され入力内容が反映される()
    {
        $category = Category::factory()->create(['content' => '商品のお届けについて']);
        $tag = Tag::factory()->create(['name' => '質問']);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '千葉県船橋市',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');

        $response->assertSee('山田 太郎');
        $response->assertSee('男性');
        $response->assertSee('test@example.com');
        $response->assertSee('09012345678');
        $response->assertSee('千葉県船橋市');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('テストビル');
        $response->assertSee('お問い合わせ内容');
    }

    // 氏（last_name）が空
    public function test_確認ページで氏がない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '太郎',
            'last_name' => '', // 必須エラー
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['last_name']);
    }

    // 名（first_name）が空
    public function test_確認ページで名がない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '', // 必須エラー
            'last_name' => '山田',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['first_name']);
    }

    public function test_氏名が255文字を超える場合はバリデーションエラーになる()
    {
        $longName = str_repeat('山', 256);

        $response = $this->post('/contacts/confirm', [
            'first_name' => $longName,
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['first_name']);
    }

    // 性別（gender）が空
    public function test_確認ページで性別がない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => '', // 必須エラー
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['gender']);
    }

    // メール（email）が空
    public function test_確認ページでメールがない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => '', // 必須エラー
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    // 電話番号（tel）が空
    public function test_確認ページで電話番号がない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'test@example.com',
            'tel' => '', // 必須エラー
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tel']);
    }

    // 住所（address）が空
    public function test_確認ページで住所がない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '', // 必須エラー
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['address']);
    }

    // カテゴリ（category_id）が空
    public function test_確認ページでカテゴリがない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => '', // 必須エラー
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['category_id']);
    }

    // detail（お問い合わせ内容）が空
    public function test_確認ページでお問い合わせ内容がない場合はリダイレクトされる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => '', // 必須エラー
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['detail']);
    }

    public function test_送信処理で_d_b保存されサンクスページへリダイレクトされる()
    {
        $category = Category::factory()->create(['content' => '商品のお届けについて']);
        $tag = Tag::factory()->create(['name' => '質問']);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts', $data);

        // thanks にリダイレクトされること
        $response->assertRedirect('/thanks');

        // contacts テーブルに保存されていること
        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '広島県広島市',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容',
        ]);

        // contact_tag テーブルにタグが保存されていること
        $contact = Contact::first();
        $this->assertTrue($contact->tags()->where('tags.id', $tag->id)->exists());
    }

    public function test_送信処理でバリデーションエラー時はリダイレクトされる()
    {
        $response = $this->post('/contacts', [
            'first_name' => '', // 必須エラー
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['first_name']);
    }

    public function test_性別が未選択の場合はバリデーションエラーになる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => '', // 未選択
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['gender']);
    }

    public function test_メール形式が不正の場合はバリデーションエラーになる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'invalid-email',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_電話番号が不正の場合はバリデーションエラーになる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => 'abc123', // 不正
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tel']);
    }

    public function test_電話番号が9桁の場合はバリデーションエラーになる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '090123456', // 9桁 → 不正
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tel']);
    }

    public function test_電話番号が12桁の場合はバリデーションエラーになる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '090123456789', // 12桁 → 不正
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tel']);
    }

    public function test_お問い合わせ内容が120文字を超える場合はバリデーションエラーになる()
    {
        $longDetail = str_repeat('あ', 121);

        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 1,
            'detail' => $longDetail,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['detail']);
    }

    public function test_buildingが空の場合は確認画面に表示されない()
    {
        $category = Category::factory()->create(['content' => '商品のお届けについて']);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'building' => '', // 空
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容',
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
    }

    public function test_カテゴリが未選択の場合はバリデーションエラーになる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => '1',
            'address' => '広島県広島市',
            'category_id' => '',
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['category_id']);
    }

    public function test_存在しないカテゴリを選択した場合はバリデーションエラーになる()
    {
        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'gender' => 1,
            'address' => '広島県広島市',
            'category_id' => 9999, // 存在しないID
            'detail' => 'お問い合わせ内容',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['category_id']);
    }
}
