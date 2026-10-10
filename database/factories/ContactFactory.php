<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Contact; //Contactモデルを使用するために追加
use App\Models\Category; //Categoryモデルを使用するために追加

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Fakerを使用してダミーデータを生成
            'first_name' => $this->faker->firstName(), // 名前を生成
            'last_name' => $this->faker->lastName(), // 苗字を生成
            'gender' => $this->faker->randomElement([1, 2,3]), // 1:男性、2:女性、3:その他
            'email' => $this->faker->unique()->safeEmail(), // ユニークなメールアドレスを生成
            'tel' => $this->faker->numerify(
                $this->faker->randomElement(['##########', '###########'])
            ), // ハイフン無しの10桁または11桁の電話番号を生成
            'address' => $this->faker->address(), // 住所を生成
            'building' => $this->faker->optional()->secondaryAddress(), // 建物名は任意で生成
            'detail' => $this->faker->text(120), // 120文字以内のテキストを生成
            'category_id' => Category::inRandomOrder()->first()->id, // ランダムにカテゴリIDを取得
        ];
    }
}
