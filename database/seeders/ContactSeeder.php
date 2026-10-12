<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Tag; // Contactモデルを使用するために追加
// Categoryモデルを使用するために追加
use Illuminate\Database\Seeder; // Tagモデルを使用するために追加

// Fakerを使用するために追加

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Contact::factory()->count(20)->create()->each(function ($contact) {
            // ランダムにタグを付与
            $tags = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $contact->tags()->attach($tags);
        });

    }
}
