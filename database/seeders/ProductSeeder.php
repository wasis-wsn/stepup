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

        $defaultSizes = ['39', '40', '41', '42', '43', '44', '45'];

        $products = [
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP AeroRun Pro',
                'description' => 'Lightweight running shoes for daily training with responsive cushion technology, engineered breathable knit mesh, and shock-absorbing EVA midsole.',
                'detail' => "• Engineered Breathable Knit Mesh Upper\n• Ultra-lightweight EVA Dual-Density Midsole\n• Anatomical Arch Support with Padded Collar\n• High-abrasion Rubber Traction Outsole",
                'sizes' => $defaultSizes,
                'color' => 'Triple White',
                'discount' => 10,
                'price' => 450000.00,
                'image' => 'products/aerorun-white.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Urban Classic',
                'description' => 'Timeless minimalist low-top lifestyle sneaker crafted with matte black canvas, premium suede overlays, and a durable natural gum rubber cupsole.',
                'detail' => "• Heavyweight 12oz Duck Canvas & Suede Overlays\n• Vintage Vulcanized Natural Gum Rubber Sole\n• Cushioned OrthoLite Insole for All-Day Wear\n• Minimalist Low-Profile Court Silhouette",
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'color' => 'Matte Black / Gum',
                'discount' => 15,
                'price' => 350000.00,
                'image' => 'products/urban-black-gum.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP Velocity Blast',
                'description' => 'High-propulsion marathon runner featuring aerodynamic upper, carbon-infused speed plate, and ultra-responsive high-energy return foam.',
                'detail' => "• Full-length Carbon Fiber Speed Propulsion Plate\n• High-energy Pebax Superfoam Cushioning\n• Ultra-thin Micro-monofilament Race Mesh\n• Wet-traction Road Racing Grip Pattern",
                'sizes' => $defaultSizes,
                'color' => 'Ice White / Pure Platinum',
                'discount' => 0,
                'price' => 699000.00,
                'image' => 'products/aerorun-white.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Street Glide',
                'description' => 'Ultra-flexible low-profile urban walker with memory-foam cushioned insole and breathable textile construction for effortless movement.',
                'detail' => "• Soft Microfiber Suede & Breathable Textile\n• Triple-density Cloud Memory Foam Insole\n• Flex-groove Lightweight Rubber Outsole\n• Reinforced Toe Cap and Heel Counter",
                'sizes' => ['39', '40', '41', '42', '43', '44'],
                'color' => 'Onyx Black',
                'discount' => 0,
                'price' => 389000.00,
                'image' => 'products/urban-black-gum.jpg',
            ],
            [
                'category_id' => $formal?->id,
                'name' => 'STEPUP Oxford Monarch',
                'description' => 'Handcrafted dress oxford made from genuine full-grain Italian calfskin with hand-burnished cognac finish and closed lacing system.',
                'detail' => "• Hand-selected Full-grain Italian Calfskin Leather\n• Goodyear Welted Construction for Lifetime Longevity\n• Hand-burnished Artisan Antique Cognac Finish\n• Stacked Leather Heel with Protective Rubber Tap",
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'color' => 'Burnished Cognac Brown',
                'discount' => 0,
                'price' => 850000.00,
                'image' => 'products/oxford-monarch-cognac.jpg',
            ],
            [
                'category_id' => $formal?->id,
                'name' => 'STEPUP Derby Executive',
                'description' => 'Distinguished open-lacing derby shoe tailored for executives with stacked wooden heel, supple crust leather, and ergonomic arch support.',
                'detail' => "• Premium Crust Calf Leather with Soft Lustre\n• Open Derby Facing for High-instep Comfort\n• Breathable Sheepskin Lining with Arch Cushion\n• Chiseled Classic Almond Toe Silhouette",
                'sizes' => ['40', '41', '42', '43', '44'],
                'color' => 'Rich Chestnut',
                'discount' => 5,
                'price' => 920000.00,
                'image' => 'products/oxford-monarch-cognac.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP CloudPacer',
                'description' => 'Max cushioning long-distance recovery runner designed to neutralize heavy impact on tarmac with dual-density cloud foam technology.',
                'detail' => "• Dual-density Plush Cloud Cushioning\n• Engineered Elastic Knitted Sock Collar\n• Ergonomic Rocker Geometry for Smooth Transitions\n• Reflective Night-run Heel Elements",
                'sizes' => $defaultSizes,
                'color' => 'Cloud White',
                'discount' => 20,
                'price' => 540000.00,
                'image' => 'products/aerorun-white.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Retro Low Vintage',
                'description' => 'Heritage 90s court silhouette sneaker with off-white pebbled leather, sage green suede overlays, and reinforced vintage rubber cupsole.',
                'detail' => "• Tumbled Pebbled Calf Leather with Suede Accents\n• Perforated Toe Box for Ventilation\n• Vintage Cream Colored Cupsole with Stitch Detailing\n• Padded Terry Cloth Tongue and Ankle Collar",
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'color' => 'Off-White / Sage Green',
                'discount' => 0,
                'price' => 420000.00,
                'image' => 'products/retro-vintage-low.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP Trail Strider',
                'description' => 'Technical all-weather trail running shoe equipped with waterproof ripstop mesh, mudguard barrier, and deep-lug traction outsole.',
                'detail' => "• Weatherproof Ballistic Ripstop Mesh Upper\n• Protective Rock-plate Forefoot Shield\n• 5mm Multi-directional Lugged Traction Rubber\n• Quick-lace System with Gusseted Tongue",
                'sizes' => $defaultSizes,
                'color' => 'Olive Green / Charcoal',
                'discount' => 0,
                'price' => 580000.00,
                'image' => 'products/trail-strider-green.jpg',
            ],
            [
                'category_id' => $casual?->id,
                'name' => 'STEPUP Canvas Breeze',
                'description' => 'Classic lightweight court sneaker in premium aged leather and suede detailing for relaxed weekend styling.',
                'detail' => "• Lightweight Breathable Summer Canvas Upper\n• Removable Antimicrobial Moisture-wicking Insole\n• Vulcanized Rubber Outsole with Herringbone Tread\n• Reinforced Brass Eyelets with Flat Cotton Laces",
                'sizes' => ['38', '39', '40', '41', '42', '43'],
                'color' => 'Chalk White / Vintage Green',
                'discount' => 10,
                'price' => 275000.00,
                'image' => 'products/retro-vintage-low.jpg',
            ],
            [
                'category_id' => $formal?->id,
                'name' => 'STEPUP Loafer Prestige',
                'description' => 'Slip-on luxury penny loafer made from buttery-soft black box calf leather with hand-stitched moccasin apron toe and leather sole.',
                'detail' => "• French Box Calfskin with Mirror Finish\n• Hand-stitched Traditional Moccasin Apron Toe\n• Genuine Channel-stitched Vegetable-tanned Leather Sole\n• Slip-on Architectural Penny Cutout Strap",
                'sizes' => ['39', '40', '41', '42', '43', '44', '45'],
                'color' => 'Classic Deep Black',
                'discount' => 0,
                'price' => 780000.00,
                'image' => 'products/loafer-prestige-black.jpg',
            ],
            [
                'category_id' => $running?->id,
                'name' => 'STEPUP Sprint Elite',
                'description' => 'Hyper-lightweight track and sprint racing flat with anatomical heel support, aggressive forefoot grip, and featherlight construction.',
                'detail' => "• Sub-180g Ultra-lightweight Monofilament Weave\n• Aggressive Forefoot Track Propulsion Pods\n• Minimalist Molded Heel Counter for Locked-down Fit\n• Laser-cut Ventilation Zones Across Midfoot",
                'sizes' => $defaultSizes,
                'color' => 'Pure White / Silver Accents',
                'discount' => 0,
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
