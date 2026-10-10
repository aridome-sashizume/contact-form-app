<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Contact; //Contactモデルを使用するために追加
use App\Models\Category; //Categoryモデルを使用するために追加   
use App\Models\Tag; //Tagモデルを使用するために追加

use Faker\Factory as Faker; //Fakerを使用するために追加

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
