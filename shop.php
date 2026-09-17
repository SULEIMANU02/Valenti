<?php
/**
 * Valenti Atelier - Shop Catalog & Filtering Engine (Light Mode & Responsive)
 * Multi-facet filtering: Category, Gender, Price, Badge, and Full-Text Search.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();

// Filter parameters
$search = trim($_GET['search'] ?? '');
$categorySlug = trim($_GET['category'] ?? '');
$gender = trim($_GET['gender'] ?? '');
$badge = trim($_GET['badge'] ?? '');
$sort = trim($_GET['sort'] ?? 'newest');
$maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 600.0;

// Base query construction
$sql = "
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE 1=1
";
$params = [];

if ($search !== '') {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($categorySlug !== '') {
    $sql .= " AND c.slug = ?";
    $params[] = $categorySlug;
}

if ($gender !== '') {
    $sql .= " AND (p.gender = ? OR p.gender = 'Unisex')";
    $params[] = $gender;
}

if ($badge !== '') {
    $sql .= " AND p.badge = ?";
    $params[] = $badge;
}

$sql .= " AND (CASE WHEN p.sale_price IS NOT NULL THEN p.sale_price ELSE p.price END) <= ?";
$params[] = $maxPrice;

// Sorting logic
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY (CASE WHEN p.sale_price IS NOT NULL THEN p.sale_price ELSE p.price END) ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY (CASE WHEN p.sale_price IS NOT NULL THEN p.sale_price ELSE p.price END) DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY p.name ASC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch all categories for filter sidebar
$allCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$pageTitle = "Wardrobe Catalog & Collections";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 36px; padding-bottom: 80px;">
  
  <!-- Breadcrumb & Title -->
  <div style="margin-bottom: 24px;">
    <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent-gold); margin-bottom: 6px; font-weight: 600;">
      <a href="/index.php" style="color: var(--text-muted);">Home</a> / <span>Collection</span>
    </div>
    <h1 style="font-size: clamp(1.8rem, 3.5vw, 2.2rem); color: var(--text-primary);">Atelier Catalog</h1>
  </div>

  <!-- Topbar Search & Sort -->
  <form action="/shop.php" method="GET" class="shop-topbar">
    <?php if ($categorySlug): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($categorySlug); ?>"><?php endif; ?>
    <?php if ($gender): ?><input type="hidden" name="gender" value="<?php echo htmlspecialchars($gender); ?>"><?php endif; ?>
    <?php if ($badge): ?><input type="hidden" name="badge" value="<?php echo htmlspecialchars($badge); ?>"><?php endif; ?>

    <!-- Search Box -->
    <div class="shop-search-bar">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--text-muted);">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="search" placeholder="Search by name, fabric, style..." value="<?php echo htmlspecialchars($search); ?>">
    </div>

    <!-- Product Count & Sort Dropdown -->
    <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
      <span style="font-size: 0.85rem; color: var(--text-muted);">
        Showing <strong><?php echo count($products); ?></strong> styles
      </span>

      <select name="sort" class="sort-select" onchange="this.form.submit()">
        <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest First</option>
        <option value="price_asc" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
        <option value="price_desc" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
        <option value="name_asc" <?php echo ($sort === 'name_asc') ? 'selected' : ''; ?>>Alphabetical (A-Z)</option>
      </select>
    </div>
  </form>

  <div class="shop-layout">
    
    <!-- Filter Sidebar -->
    <aside class="shop-sidebar">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <span style="font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-primary);">Filters</span>
        <a href="/shop.php" style="font-size: 0.78rem; color: var(--accent-gold); text-decoration: underline; font-weight: 600;">Reset All</a>
      </div>

      <form action="/shop.php" method="GET" id="filterForm">
        <?php if ($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
        <?php if ($sort): ?><input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>"><?php endif; ?>

        <!-- Category Filter -->
        <div class="filter-group">
          <div class="filter-title">Category</div>
          <ul class="filter-list">
            <li class="filter-item">
              <label>
                <input type="radio" name="category" value="" <?php echo empty($categorySlug) ? 'checked' : ''; ?> onchange="this.form.submit()">
                All Categories
              </label>
            </li>
            <?php foreach ($allCategories as $cat): ?>
              <li class="filter-item">
                <label>
                  <input type="radio" name="category" value="<?php echo htmlspecialchars($cat['slug']); ?>" <?php echo ($categorySlug === $cat['slug']) ? 'checked' : ''; ?> onchange="this.form.submit()">
                  <?php echo htmlspecialchars($cat['name']); ?>
                </label>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Gender / Department Filter -->
        <div class="filter-group">
          <div class="filter-title">Department</div>
          <ul class="filter-list">
            <li class="filter-item">
              <label>
                <input type="radio" name="gender" value="" <?php echo empty($gender) ? 'checked' : ''; ?> onchange="this.form.submit()">
                All Departments
              </label>
            </li>
            <li class="filter-item">
              <label>
                <input type="radio" name="gender" value="Men" <?php echo ($gender === 'Men') ? 'checked' : ''; ?> onchange="this.form.submit()">
                Men's Tailoring
              </label>
            </li>
            <li class="filter-item">
              <label>
                <input type="radio" name="gender" value="Women" <?php echo ($gender === 'Women') ? 'checked' : ''; ?> onchange="this.form.submit()">
                Women's Silks
              </label>
            </li>
            <li class="filter-item">
              <label>
                <input type="radio" name="gender" value="Unisex" <?php echo ($gender === 'Unisex') ? 'checked' : ''; ?> onchange="this.form.submit()">
                Unisex Atelier
              </label>
            </li>
          </ul>
        </div>

        <!-- Price Ceiling Slider -->
        <div class="filter-group">
          <div class="filter-title">Max Price: <span style="color: var(--text-primary); font-weight: 700;"><?php echo formatPrice($maxPrice); ?></span></div>
          <input type="range" name="max_price" min="50" max="600" step="25" value="<?php echo $maxPrice; ?>" style="width: 100%; accent-color: var(--accent-gold); cursor: pointer;" onchange="this.form.submit()">
          <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-top: 6px;">
            <span><?php echo CURRENCY_SYMBOL; ?>50</span>
            <span><?php echo CURRENCY_SYMBOL; ?>600</span>
          </div>
        </div>

        <!-- Special Collections / Badges -->
        <div class="filter-group">
          <div class="filter-title">Curation</div>
          <ul class="filter-list">
            <li class="filter-item">
              <label>
                <input type="radio" name="badge" value="" <?php echo empty($badge) ? 'checked' : ''; ?> onchange="this.form.submit()">
                All Pieces
              </label>
            </li>
            <li class="filter-item">
              <label>
                <input type="radio" name="badge" value="SALE" <?php echo ($badge === 'SALE') ? 'checked' : ''; ?> onchange="this.form.submit()">
                On Sale (Discounted)
              </label>
            </li>
            <li class="filter-item">
              <label>
                <input type="radio" name="badge" value="NEW" <?php echo ($badge === 'NEW') ? 'checked' : ''; ?> onchange="this.form.submit()">
                New Arrivals
              </label>
            </li>
            <li class="filter-item">
              <label>
                <input type="radio" name="badge" value="BESTSELLER" <?php echo ($badge === 'BESTSELLER') ? 'checked' : ''; ?> onchange="this.form.submit()">
                Bestsellers
              </label>
            </li>
          </ul>
        </div>

      </form>
    </aside>

    <!-- Products Grid Column -->
    <main>
      <?php if (empty($products)): ?>
        <div style="background: var(--bg-surface); border: 1px dashed var(--border-medium); border-radius: var(--radius-md); padding: 70px 20px; text-align: center;">
          <h3 style="font-size: 1.4rem; margin-bottom: 12px; color: var(--text-primary);">No Styles Match Your Filter Criteria</h3>
          <p style="color: var(--text-secondary); margin-bottom: 24px;">Try broadening your price range, clearing the search query, or resetting filters.</p>
          <a href="/shop.php" class="btn btn-outline">Reset All Filters</a>
        </div>
      <?php else: ?>
        <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));">
          <?php foreach ($products as $prod): ?>
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
                  <a href="/product.php?id=<?php echo $prod['id']; ?>" class="btn btn-primary btn-sm btn-block">
                    View &amp; Customize
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
