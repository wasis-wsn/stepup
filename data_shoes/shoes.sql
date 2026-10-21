-- STEPUP Shoes Catalog SQL Dump
-- Use with phpMyAdmin or MySQL CLI

-- 1. Ensure Categories Exist
INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Running Shoes', 'running-shoes', NOW(), NOW()),
(2, 'Casual Sneakers', 'casual-sneakers', NOW(), NOW()),
(3, 'Formal Shoes', 'formal-shoes', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 2. Insert Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'STEPUP AeroRun Pro', 'stepup-aerorun-pro', 'Lightweight running shoes for daily training with responsive cushion technology.', 450000.00, 'products/aerorun-white.jpg', NOW(), NOW()),
(2, 2, 'STEPUP Urban Classic', 'stepup-urban-classic', 'Timeless minimalist low-top lifestyle sneaker with matte black canvas and natural gum rubber outsole.', 350000.00, 'products/urban-black-gum.jpg', NOW(), NOW()),
(3, 1, 'STEPUP Velocity Blast', 'stepup-velocity-blast', 'High-propulsion marathon runner featuring aerodynamic upper and carbon-infused speed plate.', 699000.00, 'products/aerorun-white.jpg', NOW(), NOW()),
(4, 2, 'STEPUP Street Glide', 'stepup-street-glide', 'Ultra-flexible low-profile urban walker with memory-foam cushioned insole for all-day comfort.', 389000.00, 'products/urban-black-gum.jpg', NOW(), NOW()),
(5, 3, 'STEPUP Oxford Monarch', 'stepup-oxford-monarch', 'Handcrafted dress oxford made from genuine full-grain Italian calfskin with hand-burnished cognac finish.', 850000.00, 'products/oxford-monarch-cognac.jpg', NOW(), NOW()),
(6, 3, 'STEPUP Derby Executive', 'stepup-derby-executive', 'Distinguished open-lacing derby shoe tailored for executives with stacked wooden heel.', 920000.00, 'products/oxford-monarch-cognac.jpg', NOW(), NOW()),
(7, 1, 'STEPUP CloudPacer', 'stepup-cloudpacer', 'Max cushioning recovery runner designed to neutralize heavy impact on tarmac.', 540000.00, 'products/aerorun-white.jpg', NOW(), NOW()),
(8, 2, 'STEPUP Retro Low Vintage', 'stepup-retro-low-vintage', 'Heritage 90s court silhouette sneaker with off-white pebbled leather and sage green suede overlays.', 420000.00, 'products/retro-vintage-low.jpg', NOW(), NOW()),
(9, 1, 'STEPUP Trail Strider', 'stepup-trail-strider', 'Technical all-weather trail running shoe equipped with waterproof ripstop mesh and deep-lug traction outsole.', 580000.00, 'products/trail-strider-green.jpg', NOW(), NOW()),
(10, 2, 'STEPUP Canvas Breeze', 'stepup-canvas-breeze', 'Classic lightweight court sneaker in premium aged leather and suede detailing for relaxed styling.', 275000.00, 'products/retro-vintage-low.jpg', NOW(), NOW()),
(11, 3, 'STEPUP Loafer Prestige', 'stepup-loafer-prestige', 'Slip-on luxury penny loafer made from buttery-soft black box calf leather with hand-stitched moccasin apron toe.', 780000.00, 'products/loafer-prestige-black.jpg', NOW(), NOW()),
(12, 1, 'STEPUP Sprint Elite', 'stepup-sprint-elite', 'Hyper-lightweight track and sprint racing flat with anatomical heel support and aggressive forefoot grip.', 750000.00, 'products/aerorun-white.jpg', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `category_id`=VALUES(`category_id`),
    `name`=VALUES(`name`),
    `description`=VALUES(`description`),
    `price`=VALUES(`price`),
    `image`=VALUES(`image`),
    `updated_at`=NOW();
