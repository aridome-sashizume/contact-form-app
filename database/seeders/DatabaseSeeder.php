<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // UserSeederを呼び出す
        $this->call(UserSeeder::class);

        // CategorySeederを呼び出す
        $this->call(CategorySeeder::class);

        // TagSeederを呼び出す
        $this->call(TagSeeder::class);

        // ContactSeederを呼び出す
        $this->call(ContactSeeder::class);
    }
}
