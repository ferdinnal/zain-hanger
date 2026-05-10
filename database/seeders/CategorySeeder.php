<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Gantungan',
                'slug'        => 'gantungan',
                'description' => 'Hanger kayu premium untuk segala ukuran dan kebutuhan.',
                'image_url'   => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'name'        => 'Rak',
                'slug'        => 'rak',
                'description' => 'Solusi penyimpanan pakaian yang artistik dan fungsional.',
                'image_url'   => 'https://images.unsplash.com/photo-1594498653385-d5172c532c00?q=80&w=800',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'name'        => 'Perabot Kayu',
                'slug'        => 'perabot-kayu',
                'description' => 'Koleksi perabot kayu berkualitas tinggi untuk rumah dan bisnis.',
                'image_url'   => 'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?q=80&w=800',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $this->command->info('✅ Categories seeded (3).');
    }
}
