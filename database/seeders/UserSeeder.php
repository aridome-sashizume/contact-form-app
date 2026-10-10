<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User; //Userモデルを使用するために追加

use Illuminate\Support\Facades\Hash; //Hashファサードを使用するために追加

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ユーザ情報の初期管理者データを作成
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // パスワードをハッシュ化
        ]);
    }
}
