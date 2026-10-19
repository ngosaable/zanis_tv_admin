<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Starter categories so the admin forms (videos, live channels) have something to pick.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Movies',       'type' => 'Movie'],
            ['name' => 'TV Series',    'type' => 'Series'],
            ['name' => 'Documentaries','type' => 'Documentary'],
            ['name' => 'News',         'type' => 'Live'],
            ['name' => 'Sports',       'type' => 'Live'],
            ['name' => 'Kids',         'type' => 'Live'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['type' => $category['type'], 'status' => true]
            );
        }
    }
}
