<?php
/**
 * Valenti Atelier - Storefront Homepage (Editorial Light Mode)
 * Course Project: SEN 803 Software Technology
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();

// Handle newsletter subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'subscribe_newsletter') {
    $email = filter_var($_POST['newsletter_email'] ?? '', FILTER_VALIDATE_EMAIL);
    if ($email) {
        try {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO newsletter_subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
            setFlash('success', 'Thank you for joining the Valenti Atelier Private Club. Use code WELCOME10 for 10% off.');
        } catch (Exception $e) {
            setFlash('info', 'You are already subscribed to our newsletter.');
        }
    } else {
        setFlash('error', 'Please enter a valid email address.');
    }
    redirect('/index.php');
}

// Fetch categories with product counts
$catStmt = $pdo->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.id ASC
");
$categories = $catStmt->fetchAll();

// Fetch featured products
$featStmt = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.featured = 1 
    ORDER BY p.id ASC 
    LIMIT 8
");
$featuredProducts = $featStmt->fetchAll();

// Fetch recent customer reviews
$revStmt = $pdo->query("
    SELECT r.*, p.name as product_name, p.slug as product_slug 
    FROM reviews r 
    JOIN products p ON r.product_id = p.id 
    ORDER BY r.id DESC 
    LIMIT 3
");
$testimonials = $revStmt->fetchAll();

$pageTitle = "Artisanal Luxury & Contemporary Fashion";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container hero-grid">
    <div class="hero-content">
      <span class="hero-eyebrow">Autumn / Winter Atelier Capsule</span>
      <h1 class="hero-title">Architectural Tailoring &amp; Pure Silk Silhouettes</h1>
      <p class="hero-description">
        Precision-cut Italian garments engineered with timeless drape and modernist poise. Handcrafted from virgin Merino wool, burnished full-grain calfskin, and 22-momme pure silk.
      </p>
      <div class="hero-cta-group">
        <a href="/shop.php" class="btn btn-primary">
          Explore Collection
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
        <a href="/shop.php?category=outerwear" class="btn btn-outline">View Outerwear</a>
      </div>
    </div>

    <!-- Hero Showcase Card -->
    <div class="hero-showcase">
      <div class="hero-card-preview">
        <img src="/assets/images/products/leather_bomber.jpg" alt="Burnished Calfskin Bomber">
        <div class="hero-floating-badge">
          <div>
            <div style="font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; color: var(--accent-gold); font-weight: 700;">Curator's Choice</div>
            <div style="font-weight: 700; color: var(--text-primary); font-size: 0.95rem;">Burnished Calfskin Bomber</div>
          </div>
          <div style="font-weight: 700; color: var(--accent-gold); font-size: 1.15rem;"><?php echo formatPrice(485); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Value Propositions Bar -->
<div class="props-bar">
  <div class="container">
    <div class="props-grid">
      
      <div class="prop-card">
        <div class="prop-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="1" y="3" width="15" height="13"></rect>
            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
            <circle cx="5.5" cy="18.5" r="2.5"></circle>
            <circle cx="18.5" cy="18.5" r="2.5"></circle>
          </svg>
        </div>
        <div>
          <div class="prop-title">Complimentary Delivery</div>
          <div class="prop-desc">Doorstep delivery on orders over <?php echo formatPrice(FREE_SHIPPING_THRESHOLD); ?>.</div>
        </div>
      </div>

      <div class="prop-card">
        <div class="prop-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
          </svg>
        </div>
        <div>
          <div class="prop-title">European Fabric Mills</div>
          <div class="prop-desc">Virgin Merino wool, Normandy linen, and Mulberry silk.</div>
        </div>
      </div>

      <div class="prop-card">
        <div class="prop-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
        </div>
        <div>
          <div class="prop-title">Sartorial Integrity</div>
          <div class="prop-desc">Floating canvas construction &amp; reinforced seams.</div>
        </div>
      </div>

      <div class="prop-card">
        <div class="prop-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="23 4 23 10 17 10"></polyline>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
          </svg>
        </div>
        <div>
          <div class="prop-title">30-Day Trial Returns</div>
          <div class="prop-desc">Prepaid return packaging included with every order.</div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Category Showcases -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Curation By Department</span>
      <h2 class="section-title">The Seasonal Wardrobe</h2>
      <p class="section-subtitle">Explore carefully balanced capsules designed to seamlessly integrate into high-rotation dressing.</p>
    </div>

    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="/shop.php?category=<?php echo urlencode($cat['slug']); ?>" class="category-card">
          <img src="/<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
          <div class="category-overlay">
            <h3 class="cat-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
            <span class="cat-count"><?php echo $cat['product_count']; ?> Selected Styles &rarr;</span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Featured Products Grid -->
<section class="section" id="featured" style="background: var(--bg-surface); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Signature Works</span>
      <h2 class="section-title">Featured Atelier Pieces</h2>
      <p class="section-subtitle">Our most sought-after silhouettes, crafted with uncompromising quality.</p>
    </div>

    <div class="product-grid">
      <?php foreach ($featuredProducts as $prod): ?>
        <div class="product-card">
          <?php if (!empty($prod['badge'])): ?>
            <span class="product-badge-tag <?php echo strtolower($prod['badge']); ?>"><?php echo htmlspecialchars($prod['badge']); ?></span>
          <?php endif; ?>

          <div class="product-image-wrap">
            <a href="/product.php?id=<?php echo $prod['id']; ?>">
              <img src="/<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>">
            </a>
          </div>

          <div class="product-info">
            <div class="product-category-meta"><?php echo htmlspecialchars($prod['category_name']); ?> &bull; <?php echo htmlspecialchars($prod['gender']); ?></div>
            <h3 class="product-name">
              <a href="/product.php?id=<?php echo $prod['id']; ?>"><?php echo htmlspecialchars($prod['name']); ?></a>
            </h3>

            <div class="product-price-row">
              <?php if ($prod['sale_price'] !== null && $prod['sale_price'] > 0): ?>
                <span class="price-current"><?php echo formatPrice((float)$prod['sale_price']); ?></span>
                <span class="price-original"><?php echo formatPrice((float)$prod['price']); ?></span>
              <?php else: ?>
                <span class="price-current"><?php echo formatPrice((float)$prod['price']); ?></span>
              <?php endif; ?>
            </div>

            <div class="product-actions">
              <a href="/product.php?id=<?php echo $prod['id']; ?>" class="btn btn-outline btn-sm btn-block">
                View Piece
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 50px;">
      <a href="/shop.php" class="btn btn-primary">
        Browse Complete Catalog (12+ Pieces)
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </a>
    </div>
  </div>
</section>

<!-- Promotional Incentive Banner -->
<section class="section" style="padding: 70px 0;">
  <div class="container">
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f7f3ec 100%); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: clamp(30px, 5vw, 60px); display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; box-shadow: var(--shadow-sm);">
      <div>
        <span style="color: var(--accent-gold); font-size: 0.78rem; letter-spacing: 0.2em; text-transform: uppercase; font-weight: 700;">Promotional Incentive</span>
        <h2 style="font-size: clamp(1.8rem, 3vw, 2.4rem); color: var(--text-primary); margin: 12px 0;">Special Course Assessment Offer</h2>
        <p style="color: var(--text-secondary); font-size: 1.02rem; line-height: 1.75; margin-bottom: 24px;">
          Use student coupon voucher <code style="background: rgba(150, 111, 56, 0.12); color: var(--accent-gold); padding: 4px 10px; border-radius: 4px; font-weight: 700; letter-spacing: 0.08em;">SEN803</code> at checkout to redeem an instant <strong>20% academic discount</strong> across the entire catalog.
        </p>
        <a href="/shop.php" class="btn btn-primary">Redeem In Store</a>
      </div>
      <div style="text-align: right; font-family: var(--font-serif); font-size: clamp(3rem, 7vw, 5rem); line-height: 1; color: rgba(150, 111, 56, 0.2); font-weight: 800;">
        20% OFF
      </div>
    </div>
  </div>
</section>

<!-- Verified Client Testimonials -->
<section class="section" style="border-top: 1px solid var(--border-subtle); background: var(--bg-surface);">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Client Experience</span>
      <h2 class="section-title">Endorsements &amp; Reviews</h2>
      <p class="section-subtitle">Real feedback from clients investing in artisanal tailoring.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
      <?php foreach ($testimonials as $t): ?>
        <div style="background: var(--bg-main); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 24px; display: flex; flex-direction: column;">
          <div style="margin-bottom: 12px;">
            <?php echo renderStars((int)$t['rating']); ?>
          </div>
          <p style="color: var(--text-secondary); font-size: 0.92rem; font-style: italic; line-height: 1.65; margin-bottom: 18px; flex: 1;">
            &ldquo;<?php echo htmlspecialchars($t['review_text']); ?>&rdquo;
          </p>
          <div style="border-top: 1px solid var(--border-subtle); padding-top: 14px;">
            <div style="font-weight: 700; color: var(--text-primary); font-size: 0.88rem;"><?php echo htmlspecialchars($t['user_name']); ?></div>
            <div style="font-size: 0.75rem; color: var(--accent-gold); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600;">
              Verified Buyer &bull; <a href="/product.php?slug=<?php echo urlencode($t['product_slug']); ?>" style="color: var(--text-muted);"><?php echo htmlspecialchars($t['product_name']); ?></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
