<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User']
        );

        $categories = [
            'Casa' => ['Affitto', 'Bollette', 'Spese condominio'],
            'Lavoro' => ['Stipendio', 'Freelance'],
            'Spese personali' => ['Cibo', 'Trasporti', 'Salute'],
            'Rimborsi' => ['Rimborsi'],
        ];

        foreach ($categories as $parentName => $children) {
            $parent = Category::firstOrCreate([
                'name' => $parentName,
                'parent_id' => null,
            ]);

            foreach ($children as $childName) {
                Category::firstOrCreate([
                    'name' => $childName,
                    'parent_id' => $parent->id,
                ]);
            }
        }

        $tags = [
            'casa',
            'lavoro',
            'rimborsi',
            'spesa-fissa',
            'urgente',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }
    }
}
