<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uniqueKeys = collect();
        for ($i = 0; $i < 40000; $i++) {
            $uniqueKeys->push('key_' . $i);
        }

        $languages = Language::all();
        $translations = [];

        foreach ($uniqueKeys as $key) {
            foreach ($languages as $language) {
                $translations[] = [
                    'key' => $key,
                    'value' => fake()->sentence,
                    'language_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (count($translations) >= 5000) {
                Translation::insert($translations);
                $translations = [];
            }
        }

        if (!empty($translations)) {
            Translation::insert($translations);
        }
    }
}
