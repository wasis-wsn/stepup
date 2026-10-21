<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $running = Category::where('slug', 'running-shoes')->first();
        $casual = Category::where('slug', 'casual-sneakers')->first();
        $formal = Category::where('slug', 'formal-shoes')->first();

        $products = [
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP AeroRun Pro',
                'description' => 'Lightweight running shoes for daily training with responsive cushion technology.',
                'price' => 450000.00,
                'image' => 'products/aerorun-white.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Urban Classic',
                'description' => 'Timeless minimalist low-top sneaker made for versatile everyday street wear.',
                'price' => 350000.00,
                'image' => 'products/urban-black-gum.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP Velocity Blast',
                'description' => 'Engineered mesh upper with carbon-infused speed plate for marathon tempo runs.',
                'price' => 699000.00,
                'image' => 'products/aerorun-white.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Street Glide',
                'description' => 'Ultra-flexible sole and breathable knit material for maximum all-day comfort.',
                'price' => 389000.00,
                'image' => 'products/urban-black-gum.jpg',
            ],
            [
                'category_id' => $formal?->id,
                'name' => 'STEPUP Oxford Monarch',
                'description' => 'Genuine full-grain leather oxford with handcrafted welt and elegant finish.',
                'price' => 850000.00,
                'image' => 'products/oxford-monarch-cognac.jpg',
            ],
            [
                'category_id' => $formal?->id,
                'name' => 'STEPUP Derby Executive',
                'description' => 'Premium sleek dress shoe with cushioned insole for formal business meetings.',
                'price' => 920000.00,
                'image' => 'products/oxford-monarch-cognac.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP CloudPacer',
                'description' => 'Max cushioning long-distance shoe designed to absorb impact on hard pavement.',
                'price' => 540000.00,
                'image' => 'products/aerorun-white.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Retro Low Vintage',
                'description' => 'Nostalgic 90s silhouette with reinforced gum rubber outsole.',
                'price' => 420000.00,
                'image' => 'products/retro-vintage-low.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP Trail Strider',
                'description' => 'Rugged multi-surface lug pattern with water-resistant ripstop upper.',
                'price' => 580000.00,
                'image' => 'products/trail-strider-green.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Canvas Breeze',
                'description' => 'Lightweight vulcanized canvas sneaker perfect for casual weekend vibes.',
                'price' => 275000.00,
                'image' => 'products/retro-vintage-low.jpg',
            ],
            [
                'category_id' => $formal?->id,
                'name' => 'STEPUP Loafer Prestige',
                'description' => 'Slip-on luxury penny loafer in supple calfskin leather.',
                'price' => 780000.00,
                'image' => 'products/loafer-prestige-black.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP Sprint Elite',
                'description' => 'Aerodynamic racing flat tailored for personal records on race day.',
                'price' => 750000.00,
                'image' => 'products/aerorun-white.jpg',
            ],
        ];

        foreach ($products as $item) {
            if ($item['category_id']) {
                // Ensure image file exists in storage disk
                if (!empty($item['image']) && !Storage::disk('public')->exists($item['image'])) {
                    $sourcePath = base_path('data_shoes/images/' . basename($item['image']));
                    if (file_exists($sourcePath)) {
                        Storage::disk('public')->put($item['image'], file_get_contents($sourcePath));
                    }
                }

                Product::updateOrCreate(
                    ['slug' => Str::slug($item['name'])],
                    $item
                );
            }
        }
    }
}
