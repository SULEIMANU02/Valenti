<?php
/**
 * Valenti Atelier - Database Seeder (Editorial Light Theme)
 * Populates categories, products, admin/customer accounts, coupons, sample orders & reviews.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

function seedDatabase(PDO $pdo): void {
    // 1. Create Product SVG visual assets
    createSampleVisualAssets();

    // Check if already seeded
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $rowCount = (int)$stmt->fetchColumn();
    if ($rowCount > 0) {
        return; // Already seeded
    }

    // 2. Seed Users
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $customerPassword = password_hash('customer123', PASSWORD_BCRYPT);

    $userStmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone, address, city, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $userStmt->execute([
        'Valenti Administrator',
        'admin@valenti.com',
        $adminPassword,
        'admin',
        '+44 20 7123 4567',
        '74 Savile Row, Mayfair',
        'London',
        'W1S 2ES'
    ]);

    $userStmt->execute([
        'Aisha Abdulsalam',
        'customer@valenti.com',
        $customerPassword,
        'customer',
        '+44 20 7946 0912',
        '14 Kensington High Street',
        'London',
        'W8 4PT'
    ]);

    // 3. Seed Categories
    $categories = [
        ['Outerwear', 'outerwear', 'Architectural coats, bombers, and weather-resistant luxury layers.', 'assets/images/cat_outerwear.jpg'],
        ['Tailoring & Suiting', 'tailoring', 'Bespoke precision blazers and pleated architectural trousers.', 'assets/images/cat_tailoring.jpg'],
        ['Knitwear & Tops', 'knitwear-tops', 'Heavyweight organic cottons and pure Mongolian cashmere sweaters.', 'assets/images/cat_tops.jpg'],
        ['Trousers & Bottoms', 'trousers', 'Tailored relaxed trousers, raw Japanese denim, and tactical pants.', 'assets/images/cat_bottoms.jpg'],
        ['Dresses & Silk', 'dresses-silk', 'Fluid silk silhouettes, evening dresses, and contemporary tailoring.', 'assets/images/cat_dresses.jpg'],
        ['Footwear & Accessories', 'accessories', 'Handcrafted leather goods, silk scarves, and minimalist accents.', 'assets/images/cat_accessories.jpg'],
    ];

    $catStmt = $pdo->prepare("INSERT INTO categories (name, slug, description, image) VALUES (?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $catStmt->execute($cat);
    }

    // 4. Seed Products
    $products = [
        [
            1, // Outerwear
            'Burnished Calfskin Leather Bomber',
            'burnished-calfskin-leather-bomber',
            'Engineered from supple full-grain Italian calfskin with a hand-burnished antique finish. Features custom matte-black Riri hardware, rib-knit storm cuffs, and a quilted silk interior lining for effortless drape.',
            485.00,
            NULL,
            'Unisex',
            'S,M,L,XL',
            'Antique Espresso,Obsidian Black',
            12,
            1,
            'HOT',
            'assets/images/products/leather_bomber.jpg'
        ],
        [
            1, // Outerwear
            'Double-Breasted Storm Trench Coat',
            'double-breasted-storm-trench-coat',
            'A modern reinterpretation of the military trench. Constructed from water-repellent gabardine twill with exaggerated peak lapels, storm flap detail, and a detachable cinch belt with horn buckles.',
            360.00,
            295.00,
            'Unisex',
            'S,M,L,XL,XXL',
            'Sand Dune Beige,Midnight Charcoal',
            18,
            1,
            'SALE',
            'assets/images/products/trench_coat.jpg'
        ],
        [
            2, // Tailoring
            'Architectural Structured Wool Blazer',
            'architectural-structured-wool-blazer',
            'Tailored in Italy with a modern relaxed drop-shoulder cut. Crafted from 100% virgin Merino wool with floating canvas construction and horn button closures. Designed to elevate both formal trousers and denim.',
            340.00,
            NULL,
            'Men',
            '38R,40R,42R,44R',
            'Dark Charcoal,Oatmeal Heather',
            8,
            1,
            'NEW',
            'assets/images/products/tailored_blazer.jpg'
        ],
        [
            2, // Tailoring
            'Pleated Wide-Leg Wool Trousers',
            'pleated-wide-leg-wool-trousers',
            'High-rise front double-pleated trousers featuring side adjusters and a fluid, sweeping drape. Spun from lightweight tropical wool for year-round breathability and structured silhouette.',
            210.00,
            NULL,
            'Unisex',
            '28,30,32,34,36',
            'Obsidian,Oatmeal,Deep Navy',
            24,
            0,
            NULL,
            'assets/images/products/wool_trousers.jpg'
        ],
        [
            3, // Knitwear & Tops
            'French Terry Heavyweight Hoodie (480 GSM)',
            'french-terry-heavyweight-hoodie',
            'The archetype of luxury streetwear. Seamlessly knitted from 480 GSM organic loopback French terry with double-needle reverse stitching, kangaroo pocket, and an oversized sculptural hood.',
            175.00,
            NULL,
            'Unisex',
            'XS,S,M,L,XL,XXL',
            'Washed Slate,Chalk White,Muted Taupe',
            35,
            1,
            'BESTSELLER',
            'assets/images/products/oversized_hoodie.jpg'
        ],
        [
            3, // Knitwear & Tops
            'Chunky Ribbed Cashmere Turtleneck',
            'chunky-ribbed-cashmere-turtleneck',
            'Sumptuous 7-gauge pure Mongolian cashmere with chunky English rib knitting. Naturally thermo-regulating with raglan sleeve ergonomics and folded collar.',
            295.00,
            250.00,
            'Women',
            'XS,S,M,L',
            'Ecru Ivory,Caramel Heather,Black',
            15,
            1,
            'SALE',
            'assets/images/products/knit_sweater.jpg'
        ],
        [
            3, // Knitwear & Tops
            'Camp-Collar Relaxed Linen Shirt',
            'camp-collar-relaxed-linen-shirt',
            'Cut from breezy Normandy linen with a garment-washed softness. Features an airy relaxed boxy silhouette, mother-of-pearl buttons, and a convertible notch collar.',
            135.00,
            NULL,
            'Men',
            'S,M,L,XL',
            'Natural Linen,Sage Green,Crisp White',
            20,
            0,
            NULL,
            'assets/images/products/linen_shirt.jpg'
        ],
        [
            3, // Knitwear & Tops
            'Supima Cotton Minimalist Essential Tee',
            'supima-cotton-minimalist-essential-tee',
            'Crafted from extra-long staple 240 GSM Supima cotton. Retains color brilliance and silky drape over countless washes. Clean seamless neck binding.',
            65.00,
            NULL,
            'Unisex',
            'S,M,L,XL,XXL',
            'Off-White,Onyx Black,Bone,Olive',
            50,
            0,
            'STAPLE',
            'assets/images/products/minimalist_tee.jpg'
        ],
        [
            5, // Dresses & Silk
            'Bias-Cut Mulberry Silk Slip Dress',
            'bias-cut-mulberry-silk-slip-dress',
            'Cut on the bias from luxurious 22-momme Mulberry silk satin for a liquid silhouette that hugs the curves. Features subtle cowl neckline and delicate adjustable micro-straps.',
            280.00,
            NULL,
            'Women',
            'XS,S,M,L',
            'Champagne Gold,Emerald Nocturne,Black Pearl',
            10,
            1,
            'LIMITED',
            'assets/images/products/silk_slip_dress.jpg'
        ],
        [
            4, // Trousers & Bottoms
            'Tactical Modular Cargo Pants',
            'tactical-modular-cargo-pants',
            'Japanese ripstop cotton blend with articulated knee darts, asymmetric bellows cargo pockets, and adjustable bungee toggles at the hems for customizable tapered styling.',
            195.00,
            NULL,
            'Unisex',
            'S,M,L,XL',
            'Army Olive,Shadow Black',
            16,
            0,
            NULL,
            'assets/images/products/cargo_pants.jpg'
        ],
        [
            1, // Outerwear / Avant-Garde
            'Structured Raw Silk Haori Kimono',
            'structured-raw-silk-haori-kimono',
            'East-meets-West sartorial tailoring. Made from heavy raw textured wild silk with dropped kimono sleeves, internal tie closures, and deep patch pockets.',
            260.00,
            220.00,
            'Unisex',
            'One Size (Fluid Fit)',
            'Indigo Raw,Stone Gray',
            7,
            1,
            'EXCLUSIVE',
            'assets/images/products/wrap_kimono.jpg'
        ],
        [
            6, // Accessories
            'Handcrafted Tuscan Leather Chelsea Boots',
            'handcrafted-tuscan-leather-chelsea-boots',
            'Goodyear welted on a sleek almond toe last in Tuscany. Hand-waxed calfskin with durable Vibram rubber lug soles and elasticated side gussets.',
            320.00,
            NULL,
            'Men',
            '40,41,42,43,44,45',
            'Deep Bourbon,Nero Black',
            14,
            1,
            'HOT',
            'assets/images/products/chelsea_boots.jpg'
        ]
    ];

    $prodStmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, sale_price, gender, sizes, colors, stock, featured, badge, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $prodStmt->execute($p);
    }

    // 5. Seed Coupons
    $coupons = [
        ['SEN803', 'percentage', 20, 50.00],
        ['WELCOME10', 'percentage', 10, 0.00],
        ['VALENTI50', 'fixed', 50, 200.00]
    ];

    $coupStmt = $pdo->prepare("INSERT INTO coupons (code, type, value, min_spend) VALUES (?, ?, ?, ?)");
    foreach ($coupons as $c) {
        $coupStmt->execute($c);
    }

    // 6. Seed Sample Orders
    $order1Number = 'ORD-' . strtoupper(substr(uniqid(), 7));
    $orderStmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone, shipping_address, shipping_city, shipping_postal, shipping_country, subtotal, discount_amount, coupon_code, shipping_fee, tax_amount, total_amount, payment_method, payment_status, order_status, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $orderStmt->execute([
        $order1Number,
        2, // customer user id
        'Aisha Abdulsalam',
        'customer@valenti.com',
        '+44 20 7946 0912',
        '14 Kensington High Street',
        'London',
        'W8 4PT',
        'United Kingdom',
        515.00,
        103.00,
        'SEN803',
        0.00, // free shipping
        30.90,
        442.90,
        'Credit/Debit Card (Visa **** 4242)',
        'Paid',
        'Dispatched',
        'Please leave with concierge if unavailable.',
        date('Y-m-d H:i:s', strtotime('-2 days'))
    ]);
    $order1Id = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, size, color, quantity, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $itemStmt->execute([$order1Id, 1, 'Burnished Calfskin Leather Bomber', 485.00, 'L', 'Antique Espresso', 1, 485.00]);
    $itemStmt->execute([$order1Id, 8, 'Supima Cotton Minimalist Essential Tee', 30.00, 'L', 'Onyx Black', 1, 30.00]);

    // Order 2 (Delivered)
    $order2Number = 'ORD-' . strtoupper(substr(uniqid(), 7));
    $orderStmt->execute([
        $order2Number,
        2,
        'Aisha Abdulsalam',
        'customer@valenti.com',
        '+44 20 7946 0912',
        '14 Kensington High Street',
        'London',
        'W8 4PT',
        'United Kingdom',
        340.00,
        0.00,
        NULL,
        0.00,
        25.50,
        365.50,
        'Card (Mastercard **** 8821)',
        'Paid',
        'Delivered',
        NULL,
        date('Y-m-d H:i:s', strtotime('-5 days'))
    ]);
    $order2Id = $pdo->lastInsertId();
    $itemStmt->execute([$order2Id, 3, 'Architectural Structured Wool Blazer', 340.00, '40R', 'Dark Charcoal', 1, 340.00]);

    // Order 3 (Recent Pending)
    $order3Number = 'ORD-' . strtoupper(substr(uniqid(), 7));
    $orderStmt->execute([
        $order3Number,
        NULL, // Guest checkout demonstration
        'Eleanor Vance',
        'eleanor.vance@example.co.uk',
        '+44 20 8912 3456',
        '42 Sloane Square, Chelsea',
        'London',
        'SW1W 8AX',
        'United Kingdom',
        280.00,
        28.00,
        'WELCOME10',
        0.00,
        18.90,
        270.90,
        'Instant Bank Transfer (FPS)',
        'Paid',
        'Processing',
        'Gift wrap requested.',
        date('Y-m-d H:i:s', strtotime('-2 hours'))
    ]);
    $order3Id = $pdo->lastInsertId();
    $itemStmt->execute([$order3Id, 9, 'Bias-Cut Mulberry Silk Slip Dress', 280.00, 'M', 'Champagne Gold', 1, 280.00]);

    // 7. Seed Reviews
    $reviews = [
        [1, 'Alexander M.', 'alex@fashionreview.io', 5, 'Exceptional leather quality. The burnished finish looks even richer in person, and the silk lining makes it glide on effortlessly. Sizing is spot on.'],
        [1, 'Julian K.', 'julian@vogue.co', 5, 'Worth every penny. The Riri zippers and rib trims give it that true artisanal feel.'],
        [3, 'Marcus Vance', 'marcus@styleforum.com', 5, 'The drape on this virgin wool blazer is tailored perfection. Works just as well with dark denim as with matching suit trousers.'],
        [5, 'David L.', 'david@urbanthreads.com', 5, 'The 480 GSM French terry is thick, heavyweight, and holds its silhouette without sagging. Easily the best hoodie in my wardrobe.'],
        [9, 'Sophie R.', 'sophie@couture.fr', 5, 'The Mulberry silk has an incredible liquid sheen. Received compliments all evening at an art gala. Absolute staple.'],
    ];

    $revStmt = $pdo->prepare("INSERT INTO reviews (product_id, user_name, user_email, rating, review_text) VALUES (?, ?, ?, ?, ?)");
    foreach ($reviews as $rev) {
        $revStmt->execute($rev);
    }
}

/**
 * Generates bespoke, luxury light-studio SVG illustrations for all products & categories
 */
function createSampleVisualAssets(): void {
    $dir = BASE_PATH . '/assets/images/products';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $svgs = [
        'leather_bomber.svg' => [
            'title' => 'CALFSKIN BOMBER',
            'subtitle' => 'Outerwear Atelier',
            'bg1' => '#F7F5F0',
            'bg2' => '#ECE7DC',
            'fill' => '#2A2420',
            'stroke' => '#966F38',
            'accent' => '#966F38',
            'icon' => '<path d="M120 180 L200 130 L280 180 L260 360 L140 360 Z" fill="#2E2822" stroke="#966F38" stroke-width="2"/><path d="M200 130 L200 360" stroke="#966F38" stroke-width="1.5" stroke-dasharray="4 3"/><path d="M120 180 L80 280 L110 300 L140 240" fill="#241E1A" stroke="#966F38" stroke-width="1.5"/><path d="M280 180 L320 280 L290 300 L260 240" fill="#241E1A" stroke="#966F38" stroke-width="1.5"/>'
        ],
        'trench_coat.svg' => [
            'title' => 'STORM TRENCH',
            'subtitle' => 'Water-Repellent Twill',
            'bg1' => '#F8F6F2',
            'bg2' => '#EAE6DC',
            'fill' => '#3D3833',
            'stroke' => '#8C6836',
            'accent' => '#8C6836',
            'icon' => '<path d="M130 150 L200 120 L270 150 L280 390 L120 390 Z" fill="#3D3833" stroke="#8C6836" stroke-width="2"/><path d="M110 260 L290 260" stroke="#8C6836" stroke-width="6"/><rect x="190" y="254" width="20" height="12" fill="none" stroke="#FAF8F5" stroke-width="2"/><path d="M165 140 L200 200 L235 140" stroke="#8C6836" stroke-width="2" fill="none"/>'
        ],
        'tailored_blazer.svg' => [
            'title' => 'MERINO BLAZER',
            'subtitle' => 'Sartorial Architecture',
            'bg1' => '#F4F6F8',
            'bg2' => '#E5EAEE',
            'fill' => '#242B30',
            'stroke' => '#56728A',
            'accent' => '#3E576D',
            'icon' => '<path d="M120 160 L200 120 L280 160 L270 370 L130 370 Z" fill="#242B30" stroke="#56728A" stroke-width="2"/><path d="M170 120 L200 240 L230 120" fill="#1A1D20" stroke="#56728A" stroke-width="1.5"/><circle cx="190" cy="270" r="3" fill="#56728A"/><circle cx="210" cy="270" r="3" fill="#56728A"/><circle cx="190" cy="310" r="3" fill="#56728A"/><circle cx="210" cy="310" r="3" fill="#56728A"/>'
        ],
        'wool_trousers.svg' => [
            'title' => 'PLEATED TROUSERS',
            'subtitle' => 'Wide-Leg Tailored',
            'bg1' => '#F5F7F5',
            'bg2' => '#E7EBE6',
            'fill' => '#232B25',
            'stroke' => '#5E7958',
            'accent' => '#4A6245',
            'icon' => '<path d="M140 140 L260 140 L280 380 L210 380 L200 230 L190 380 L120 380 Z" fill="#232B25" stroke="#5E7958" stroke-width="2"/><line x1="160" y1="140" x2="160" y2="220" stroke="#5E7958" stroke-width="1.5"/><line x1="240" y1="140" x2="240" y2="220" stroke="#5E7958" stroke-width="1.5"/>'
        ],
        'oversized_hoodie.svg' => [
            'title' => '480 GSM HOODIE',
            'subtitle' => 'French Terry Luxury',
            'bg1' => '#F6F5F8',
            'bg2' => '#E8E5EE',
            'fill' => '#2E2C33',
            'stroke' => '#7C6F99',
            'accent' => '#655685',
            'icon' => '<path d="M150 140 C150 90, 250 90, 250 140 Z" fill="#2E2C33" stroke="#7C6F99" stroke-width="2"/><path d="M110 170 L160 140 L240 140 L290 170 L270 350 L130 350 Z" fill="#2E2C33" stroke="#7C6F99" stroke-width="2"/><path d="M150 270 L250 270 L240 330 L160 330 Z" fill="#232126" stroke="#7C6F99" stroke-width="1.5"/>'
        ],
        'knit_sweater.svg' => [
            'title' => 'CASHMERE KNIT',
            'subtitle' => 'Pure Mongolian Rib',
            'bg1' => '#F8F6F2',
            'bg2' => '#EAE3D5',
            'fill' => '#3D352E',
            'stroke' => '#A17D59',
            'accent' => '#8C6641',
            'icon' => '<rect x="175" y="110" width="50" height="35" rx="5" fill="#3D352E" stroke="#A17D59" stroke-width="2"/><path d="M110 170 L175 145 L225 145 L290 170 L270 360 L130 360 Z" fill="#3D352E" stroke="#A17D59" stroke-width="2"/><line x1="160" y1="160" x2="160" y2="350" stroke="#A17D59" stroke-width="1" stroke-dasharray="2 3"/><line x1="180" y1="160" x2="180" y2="350" stroke="#A17D59" stroke-width="1" stroke-dasharray="2 3"/><line x1="220" y1="160" x2="220" y2="350" stroke="#A17D59" stroke-width="1" stroke-dasharray="2 3"/><line x1="240" y1="160" x2="240" y2="350" stroke="#A17D59" stroke-width="1" stroke-dasharray="2 3"/>'
        ],
        'linen_shirt.svg' => [
            'title' => 'CAMP-COLLAR LINEN',
            'subtitle' => 'Garment-Washed Airy',
            'bg1' => '#F4F7F6',
            'bg2' => '#E3ECE9',
            'fill' => '#25332F',
            'stroke' => '#508573',
            'accent' => '#386959',
            'icon' => '<path d="M120 160 L200 130 L280 160 L270 360 L130 360 Z" fill="#25332F" stroke="#508573" stroke-width="2"/><path d="M170 130 L200 180 L230 130" fill="none" stroke="#508573" stroke-width="2"/><circle cx="200" cy="220" r="3" fill="#508573"/><circle cx="200" cy="260" r="3" fill="#508573"/><circle cx="200" cy="300" r="3" fill="#508573"/>'
        ],
        'minimalist_tee.svg' => [
            'title' => 'SUPIMA ESSENTIAL TEE',
            'subtitle' => '240 GSM Staple Cotton',
            'bg1' => '#F6F7F9',
            'bg2' => '#E8EBEF',
            'fill' => '#272A30',
            'stroke' => '#64748B',
            'accent' => '#475569',
            'icon' => '<path d="M120 150 L170 125 C185 135, 215 135, 230 125 L280 150 L260 360 L140 360 Z" fill="#272A30" stroke="#64748B" stroke-width="2"/><path d="M120 150 L80 200 L110 220 L130 180" fill="#272A30" stroke="#64748B" stroke-width="1.5"/><path d="M280 150 L320 200 L290 220 L270 180" fill="#272A30" stroke="#64748B" stroke-width="1.5"/>'
        ],
        'silk_slip_dress.svg' => [
            'title' => 'MULBERRY SILK DRESS',
            'subtitle' => 'Bias-Cut Fluid Satin',
            'bg1' => '#FAF5F7',
            'bg2' => '#EFE3EA',
            'fill' => '#3D2937',
            'stroke' => '#B85D8D',
            'accent' => '#9B4573',
            'icon' => '<line x1="160" y1="90" x2="170" y2="150" stroke="#B85D8D" stroke-width="1.5"/><line x1="240" y1="90" x2="230" y2="150" stroke="#B85D8D" stroke-width="1.5"/><path d="M165 150 C185 160, 215 160, 235 150 L250 250 C265 310, 290 390, 290 390 L110 390 C110 390, 135 310, 150 250 Z" fill="#3D2937" stroke="#B85D8D" stroke-width="2"/>'
        ],
        'cargo_pants.svg' => [
            'title' => 'TACTICAL CARGOS',
            'subtitle' => 'Japanese Ripstop Twill',
            'bg1' => '#F7F7F2',
            'bg2' => '#ECECE0',
            'fill' => '#2D3025',
            'stroke' => '#788258',
            'accent' => '#5F6941',
            'icon' => '<path d="M140 130 L260 130 L275 380 L215 380 L200 230 L185 380 L125 380 Z" fill="#2D3025" stroke="#788258" stroke-width="2"/><rect x="105" y="220" width="30" height="40" rx="3" fill="#21241B" stroke="#788258" stroke-width="1.5"/><rect x="265" y="220" width="30" height="40" rx="3" fill="#21241B" stroke="#788258" stroke-width="1.5"/>'
        ],
        'wrap_kimono.svg' => [
            'title' => 'RAW SILK HAORI',
            'subtitle' => 'Avant-Garde Kimono Cut',
            'bg1' => '#F4F7FA',
            'bg2' => '#E2EAF2',
            'fill' => '#28323E',
            'stroke' => '#577C9E',
            'accent' => '#406382',
            'icon' => '<path d="M110 140 L200 110 L290 140 L275 370 L125 370 Z" fill="#28323E" stroke="#577C9E" stroke-width="2"/><path d="M150 125 L240 260" stroke="#577C9E" stroke-width="2"/><path d="M250 125 L180 220" stroke="#577C9E" stroke-width="2"/><path d="M60 170 L110 140 L120 280 L70 270 Z" fill="#28323E" stroke="#577C9E" stroke-width="1.5"/><path d="M340 170 L290 140 L280 280 L330 270 Z" fill="#28323E" stroke="#577C9E" stroke-width="1.5"/>'
        ],
        'chelsea_boots.svg' => [
            'title' => 'TUSCAN CHELSEA',
            'subtitle' => 'Vibram Goodyear Welted',
            'bg1' => '#F7F4F1',
            'bg2' => '#ECE3DB',
            'fill' => '#3D2D24',
            'stroke' => '#A1683E',
            'accent' => '#875128',
            'icon' => '<path d="M140 180 L200 180 L210 260 L290 320 L290 350 L130 350 L130 230 Z" fill="#3D2D24" stroke="#A1683E" stroke-width="2"/><rect x="120" y="345" width="180" height="15" rx="3" fill="#1C140F" stroke="#A1683E" stroke-width="1.5"/><path d="M160 210 C160 190, 180 190, 180 210 L185 260 L155 260 Z" fill="#1A130F"/>'
        ],
    ];

    foreach ($svgs as $filename => $data) {
        $filePath = $dir . '/' . $filename;
        $svgContent = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 480" width="100%" height="100%">
  <defs>
    <linearGradient id="grad_{$data['title']}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$data['bg1']}" />
      <stop offset="100%" stop-color="{$data['bg2']}" />
    </linearGradient>
  </defs>
  
  <!-- Background Canvas -->
  <rect width="400" height="480" fill="url(#grad_{$data['title']})" />
  
  <!-- Subtle Studio Circles -->
  <circle cx="200" cy="240" r="140" fill="none" stroke="{$data['accent']}" stroke-opacity="0.12" stroke-width="1" />
  <circle cx="200" cy="240" r="180" fill="none" stroke="{$data['accent']}" stroke-opacity="0.06" stroke-width="1" />
  <line x1="200" y1="40" x2="200" y2="440" stroke="{$data['accent']}" stroke-opacity="0.08" stroke-width="1" stroke-dasharray="4 4" />
  
  <!-- Product Silhouette Icon -->
  <g>
    {$data['icon']}
  </g>
  
  <!-- Brand Watermark & Nomenclature -->
  <text x="200" y="415" font-family="'Cinzel', 'Playfair Display', serif, sans-serif" font-size="13" font-weight="700" letter-spacing="3" fill="{$data['accent']}" text-anchor="middle">{$data['title']}</text>
  <text x="200" y="435" font-family="'Plus Jakarta Sans', 'Inter', sans-serif" font-size="10" font-weight="600" letter-spacing="2" fill="#57534E" text-anchor="middle">VALENTI ATELIER &bull; {$data['subtitle']}</text>
</svg>
SVG;
        file_put_contents($filePath, $svgContent);
    }

    // Category SVGs
    $catDir = BASE_PATH . '/assets/images';
    $catSvgs = [
        'cat_outerwear.svg' => ['OUTERWEAR', '#966F38', '#F7F5F0', '#EAE5DB'],
        'cat_tailoring.svg' => ['TAILORING', '#3E576D', '#F4F6F8', '#E5EAEE'],
        'cat_tops.svg' => ['KNITWEAR & TOPS', '#655685', '#F6F5F8', '#E8E5EE'],
        'cat_bottoms.svg' => ['TROUSERS', '#4A6245', '#F5F7F5', '#E7EBE6'],
        'cat_dresses.svg' => ['DRESSES & SILK', '#9B4573', '#FAF5F7', '#EFE3EA'],
        'cat_accessories.svg' => ['ACCESSORIES', '#875128', '#F7F4F1', '#ECE3DB']
    ];

    foreach ($catSvgs as $file => $meta) {
        $cContent = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%">
  <defs>
    <linearGradient id="catGrad_{$meta[0]}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$meta[2]}" />
      <stop offset="100%" stop-color="{$meta[3]}" />
    </linearGradient>
  </defs>
  <rect width="600" height="400" fill="url(#catGrad_{$meta[0]})" />
  <circle cx="300" cy="200" r="120" fill="none" stroke="{$meta[1]}" stroke-opacity="0.25" stroke-width="1.5" />
  <rect x="220" y="120" width="160" height="160" fill="none" stroke="{$meta[1]}" stroke-opacity="0.2" stroke-width="1" />
  <text x="300" y="195" font-family="'Cinzel', serif, sans-serif" font-size="22" font-weight="700" letter-spacing="5" fill="#17181C" text-anchor="middle">{$meta[0]}</text>
  <text x="300" y="225" font-family="'Plus Jakarta Sans', sans-serif" font-size="11" font-weight="700" letter-spacing="3" fill="{$meta[1]}" text-anchor="middle">VALENTI COLLECTION</text>
</svg>
SVG;
        file_put_contents($catDir . '/' . $file, $cContent);
    }
}

// Allow CLI direct execution
if (php_sapi_name() === 'cli') {
    $pdo = getDB();
    seedDatabase($pdo);
    echo "Light theme visual assets generated and database verified!\n";
}
