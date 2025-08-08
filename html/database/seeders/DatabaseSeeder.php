<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@digitaltolk.com',
            'password' => Hash::make('p@ssW0rd')
        ]);

        Language::create(['code' => 'en', 'name' => 'English']);
        Language::create(['code' => 'fr', 'name' => 'French']);
        Language::create(['code' => 'es', 'name' => 'Spanish']);

        Tag::create(['name' => 'mobile']);
        Tag::create(['name' => 'desktop']);
        Tag::create(['name' => 'web']);

        $this->call([
            TranslationSeeder::class
        ]);
    }
}
