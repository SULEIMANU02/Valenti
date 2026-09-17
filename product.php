<?php
/**
 * Valenti Atelier - Product Detail Page (Light Mode & Responsive)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = trim($_GET['slug'] ?? '');

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
} elseif ($slug !== '') {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
    $stmt->execute([$slug]);
} else {
    redirect('/shop.php');
}

$product = $stmt->fetch();
if (!$product) {
    setFlash('error', 'The requested product could not be located in our archive.');
    redirect('/shop.php');
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $userName = sanitize($_POST['user_name'] ?? '');
    $userEmail = sanitize($_POST['user_email'] ?? '');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $reviewText = sanitize($_POST['review_text'] ?? '');

    if (!empty($userName) && !empty($reviewText)) {
        $insStmt = $pdo->prepare("INSERT INTO reviews (product_id, user_name, user_email, rating, review_text) VALUES (?, ?, ?, ?, ?)");
        $insStmt->execute([$product['id'], $userName, $userEmail, $rating, $reviewText]);
        setFlash('success', 'Thank you. Your review has been recorded.');
    } else {
        setFlash('error', 'Please fill in your name and review remarks.');
    }
    redirect('/product.php?id=' . $product['id'] . '#reviews');
}

// Fetch reviews for this product
$revStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$revStmt->execute([$product['id']]);
$reviews = $revStmt->fetchAll();

// Calculate review average
$avgRating = 5.0;
if (!empty($reviews)) {
    $totalStars = array_sum(array_column($reviews, 'rating'));
    $avgRating = round($totalStars / count($reviews), 1);
}

// Fetch related items from the same category
$relStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
$relStmt->execute([$product['category_id'], $product['id']]);
$related = $relStmt->fetchAll();

// Parse sizes and colors
$sizes = array_map('trim', explode(',', $product['sizes']));
$colors = array_map('trim', explode(',', $product['colors']));

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 36px; padding-bottom: 80px;">

  <!-- Breadcrumbs -->
  <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent-gold); margin-bottom: 24px; font-weight: 600;">
    <a href="/index.php" style="color: var(--text-muted);">Home</a> / 
    <a href="/shop.php" style="color: var(--text-muted);">Collection</a> / 
    <a href="/shop.php?category=<?php echo urlencode($product['category_slug']); ?>" style="color: var(--text-muted);"><?php echo htmlspecialchars($product['category_name']); ?></a> / 
    <span style="color: var(--text-primary);"><?php echo htmlspecialchars($product['name']); ?></span>
  </div>

  <div class="product-detail-grid">
    
    <!-- Gallery Image Box -->
    <div>
      <div class="product-gallery-box">
        <img src="/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
      </div>
      <div style="margin-top: 14px; display: flex; gap: 10px;">
        <div style="width: 72px; height: 90px; border: 2px solid var(--accent-gold); border-radius: var(--radius-sm); overflow: hidden; background: #fff;">
          <img src="/<?php echo htmlspecialchars($product['image']); ?>" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
      </div>
    </div>

    <!-- Product Purchasing Details -->
    <div class="product-details-content">
      <div class="detail-sku">SKU: VLT-<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?> &bull; <?php echo htmlspecialchars($product['gender']); ?></div>
      <h1 class="detail-title"><?php echo htmlspecialchars($product['name']); ?></h1>
      
      <!-- Rating Header -->
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px; flex-wrap: wrap;">
        <?php echo renderStars((int)round($avgRating)); ?>
        <span style="font-size: 0.88rem; color: var(--text-secondary); font-weight: 600;">
          <?php echo $avgRating; ?> / 5.0 (<?php echo count($reviews); ?> reviews)
        </span>
        <a href="#reviews" style="font-size: 0.82rem; color: var(--accent-gold); text-decoration: underline; font-weight: 600;">Read Reviews</a>
      </div>

      <!-- Price Row -->
      <div class="detail-price-row">
        <?php if ($product['sale_price'] !== null && $product['sale_price'] > 0): ?>
          <span class="detail-price"><?php echo formatPrice((float)$product['sale_price']); ?></span>
          <span class="detail-original-price"><?php echo formatPrice((float)$product['price']); ?></span>
          <span style="background: #fef2f2; color: #dc2626; padding: 4px 10px; border-radius: var(--radius-sm); font-size: 0.78rem; font-weight: 700; letter-spacing: 0.05em; border: 1px solid #fecaca;">
            SAVE <?php echo formatPrice((float)$product['price'] - (float)$product['sale_price']); ?>
          </span>
        <?php else: ?>
          <span class="detail-price"><?php echo formatPrice((float)$product['price']); ?></span>
        <?php endif; ?>
      </div>

      <div class="detail-description">
        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
      </div>

      <!-- Add to Cart Form -->
      <form action="/cart.php" method="POST">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

        <!-- Size Options -->
        <div class="option-selector-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <span class="option-label" style="margin-bottom: 0;">Select Size</span>
            <span style="font-size: 0.75rem; color: var(--accent-gold); cursor: pointer; text-decoration: underline; font-weight: 600;">Size Guide</span>
          </div>
          <div class="size-options">
            <?php foreach ($sizes as $idx => $s): ?>
              <label class="size-pill">
                <input type="radio" name="size" value="<?php echo htmlspecialchars($s); ?>" <?php echo ($idx === 0) ? 'checked' : ''; ?>>
                <span><?php echo htmlspecialchars($s); ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Color Options -->
        <div class="option-selector-group">
          <span class="option-label">Color / Finish</span>
          <div class="color-options">
            <?php foreach ($colors as $idx => $c): ?>
              <label class="color-pill">
                <input type="radio" name="color" value="<?php echo htmlspecialchars($c); ?>" <?php echo ($idx === 0) ? 'checked' : ''; ?>>
                <span><?php echo htmlspecialchars($c); ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Stock Indicator -->
        <div class="stock-indicator">
          <span class="stock-dot"></span>
          <span>In Stock &mdash; Ready to dispatch (<?php echo $product['stock']; ?> units left)</span>
        </div>

        <!-- Quantity & Add to Cart CTA -->
        <div style="display: flex; gap: 14px; align-items: center; margin-bottom: 26px; flex-wrap: wrap;">
          <div class="qty-control" style="height: 48px;">
            <button type="button" class="qty-btn qty-dec">&minus;</button>
            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qty-input">
            <button type="button" class="qty-btn qty-inc">&plus;</button>
          </div>

          <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 220px; height: 48px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
              <line x1="3" y1="6" x2="21" y2="6"></line>
            </svg>
            Add To Shopping Bag
          </button>
        </div>

      </form>

      <!-- Technical Accordion & Value Features -->
      <div style="border-top: 1px solid var(--border-subtle); padding-top: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; font-size: 0.82rem; color: var(--text-secondary);">
          <div>
            <strong style="color: var(--text-primary); display: block; margin-bottom: 2px;">Atelier Sourcing</strong>
            Spun &amp; constructed by artisan fabric mills in Northern Italy.
          </div>
          <div>
            <strong style="color: var(--text-primary); display: block; margin-bottom: 2px;">Complimentary Returns</strong>
            Return or exchange within 30 days of arrival.
          </div>
        </div>
      </div>

    </div>

  </div>

  <!-- Customer Reviews & Submission -->
  <section id="reviews" style="margin-top: 80px; padding-top: 50px; border-top: 1px solid var(--border-subtle);">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
      
      <!-- Review List -->
      <div>
        <h3 style="font-size: 1.6rem; color: var(--text-primary); margin-bottom: 8px;">Verified Client Reviews</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 24px;">Read honest feedback from patrons of Valenti Atelier.</p>

        <?php if (empty($reviews)): ?>
          <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); padding: 24px; border-radius: var(--radius-md); color: var(--text-muted);">
            No reviews yet for this piece. Be the first to share your fitting experience below.
          </div>
        <?php else: ?>
          <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($reviews as $rev): ?>
              <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 20px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                  <span style="font-weight: 700; color: var(--text-primary);"><?php echo htmlspecialchars($rev['user_name']); ?></span>
                  <span style="font-size: 0.78rem; color: var(--text-muted);"><?php echo date('F j, Y', strtotime($rev['created_at'])); ?></span>
                </div>
                <div style="margin-bottom: 10px;">
                  <?php echo renderStars((int)$rev['rating']); ?>
                </div>
                <p style="color: var(--text-secondary); font-size: 0.92rem; line-height: 1.6;">
                  <?php echo htmlspecialchars($rev['review_text']); ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Add Review Box -->
      <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 28px; height: fit-content; box-shadow: var(--shadow-sm);">
        <h4 style="font-size: 1.25rem; color: var(--text-primary); margin-bottom: 6px;">Leave An Atelier Review</h4>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px;">How does this piece fit? Share your thoughts with other patrons.</p>

        <form action="/product.php?id=<?php echo $product['id']; ?>" method="POST">
          <input type="hidden" name="action" value="submit_review">

          <div class="form-group">
            <label class="form-label">Your Name</label>
            <input type="text" name="user_name" required placeholder="e.g. Julian K." class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label">Email Address (Optional)</label>
            <input type="email" name="user_email" placeholder="e.g. julian@example.com" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-control">
              <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; (5 - Exceptional)</option>
              <option value="4">&#9733;&#9733;&#9733;&#9733;&#9734; (4 - Great Quality)</option>
              <option value="3">&#9733;&#9733;&#9733;&#9734;&#9734; (3 - Satisfactory)</option>
              <option value="2">&#9733;&#9733;&#9734;&#9734;&#9734; (2 - Below Expectations)</option>
              <option value="1">&#9733;&#9734;&#9734;&#9734;&#9734; (1 - Poor)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Review Remarks</label>
            <textarea name="review_text" rows="3" required placeholder="Share your experience regarding tailoring, textile feel, and drape..." class="form-control"></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Post Review</button>
        </form>
      </div>

    </div>
  </section>

  <!-- Related Products -->
  <?php if (!empty($related)): ?>
    <section style="margin-top: 80px; padding-top: 50px; border-top: 1px solid var(--border-subtle);">
      <h3 style="font-size: 1.5rem; color: var(--text-primary); margin-bottom: 24px;">Coordinate With These Pieces</h3>
      <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
        <?php foreach ($related as $rel): ?>
          <div class="product-card">
            <div class="product-image-wrap">
              <a href="/product.php?id=<?php echo $rel['id']; ?>">
                <img src="/<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>">
              </a>
            </div>
            <div class="product-info">
              <h4 class="product-name">
                <a href="/product.php?id=<?php echo $rel['id']; ?>"><?php echo htmlspecialchars($rel['name']); ?></a>
              </h4>
              <div class="product-price-row">
                <span class="price-current"><?php echo formatPrice((float)($rel['sale_price'] ?? $rel['price'])); ?></span>
              </div>
              <a href="/product.php?id=<?php echo $rel['id']; ?>" class="btn btn-outline btn-sm btn-block">View Piece</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
