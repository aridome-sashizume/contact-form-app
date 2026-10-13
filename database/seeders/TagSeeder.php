<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder; // Tagモデルを使用するために追加

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // tag情報の初期データを作成
        Tag::create(['name' => '質問']);
        Tag::create(['name' => '要望']);
        Tag::create(['name' => '不具合報告']);
        Tag::create(['name' => 'ご意見']);
        Tag::create(['name' => 'その他']);

    }
}
