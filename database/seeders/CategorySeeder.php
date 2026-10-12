<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder; // Catagoryモデルを使用するために追加

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // カテゴリ情報の初期データを作成
        Category::create(['content' => '商品のお届けについて']);
        Category::create(['content' => '商品の交換について']);
        Category::create(['content' => '商品トラブル']);
        Category::create(['content' => 'ショップへのお問い合わせ']);
        Category::create(['content' => 'その他']);

    }
}
