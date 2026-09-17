<?php
/**
 * Valenti Atelier - Header Component (Light Mode & Responsive)
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$cartCount = getCartCount();
$user = currentUser();
$flash = getFlash();
$currentScript = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . BRAND_NAME : BRAND_NAME . ' — ' . BRAND_SLOGAN; ?></title>
  <meta name="description" content="Valenti Atelier — Contemporary luxury fashion, tailored silhouettes, and minimalist streetwear.">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">
</head>
<body>

  <!-- Top Announcement Bar -->
  <div class="top-notice-bar">
    <div class="container">
      Complimentary Global Shipping on Orders Over <?php echo formatPrice(FREE_SHIPPING_THRESHOLD); ?> &bull; SEN 803 Course Project
    </div>
  </div>

  <!-- Main Navigation Header -->
  <header class="main-header">
    <div class="container nav-container">
      
      <!-- Mobile Hamburger Button -->
      <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Navigation">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>

      <!-- Brand Logo -->
      <a href="/index.php" class="brand-logo">
        <span class="brand-title"><?php echo BRAND_NAME; ?></span>
        <span class="brand-subtitle">Haute Horlogerie &bull; Tailoring</span>
      </a>

      <!-- Desktop Navigation Links -->
      <nav>
        <ul class="nav-links">
          <li><a href="/index.php" class="nav-link <?php echo ($currentScript === 'index.php') ? 'active' : ''; ?>">Home</a></li>
          <li><a href="/shop.php" class="nav-link <?php echo ($currentScript === 'shop.php' && empty($_GET['category']) && empty($_GET['badge'])) ? 'active' : ''; ?>">Collection</a></li>
          <li><a href="/shop.php?category=outerwear" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] === 'outerwear') ? 'active' : ''; ?>">Outerwear</a></li>
          <li><a href="/shop.php?category=tailoring" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] === 'tailoring') ? 'active' : ''; ?>">Tailoring</a></li>
          <li><a href="/shop.php?category=knitwear-tops" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] === 'knitwear-tops') ? 'active' : ''; ?>">Knitwear</a></li>
          <li><a href="/shop.php?badge=SALE" class="nav-link <?php echo (isset($_GET['badge']) && $_GET['badge'] === 'SALE') ? 'active' : ''; ?>" style="color: #dc2626;">Sale</a></li>
        </ul>
      </nav>

      <!-- Nav Actions -->
      <div class="nav-actions">
        <!-- Search Trigger / Quick Link -->
        <a href="/shop.php" class="icon-btn" title="Search Catalog" aria-label="Search Catalog">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </a>

        <!-- Shopping Cart Icon with Badge -->
        <a href="/cart.php" class="icon-btn" title="Shopping Bag" aria-label="Shopping Bag">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 01-8 0"></path>
          </svg>
          <?php if ($cartCount > 0): ?>
            <span class="cart-count-badge"><?php echo $cartCount; ?></span>
          <?php endif; ?>
        </a>

        <!-- User / Admin Portal Link -->
        <?php if ($user): ?>
          <a href="/account.php" class="user-badge-link" title="My Account">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span><?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?></span>
          </a>
          <?php if ($user['role'] === 'admin'): ?>
            <a href="/admin/index.php" class="btn btn-sm btn-outline" style="border-color: var(--accent-gold); color: var(--accent-gold); padding: 6px 12px;" title="Admin Console">
              Admin
            </a>
          <?php endif; ?>
        <?php else: ?>
          <a href="/login.php" class="user-badge-link">
            <span>Sign In</span>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div class="mobile-nav-drawer" id="mobileNavDrawer">
      <a href="/index.php" class="nav-link">Home</a>
      <a href="/shop.php" class="nav-link">All Collections</a>
      <a href="/shop.php?category=outerwear" class="nav-link">Outerwear &amp; Coats</a>
      <a href="/shop.php?category=tailoring" class="nav-link">Tailoring &amp; Suiting</a>
      <a href="/shop.php?category=knitwear-tops" class="nav-link">Cashmere &amp; Knitwear</a>
      <a href="/shop.php?badge=SALE" class="nav-link" style="color: #dc2626;">Seasonal Sale (Discounts)</a>
      <?php if ($user): ?>
        <a href="/account.php" class="nav-link">My Account &amp; Orders (<?php echo htmlspecialchars($user['name']); ?>)</a>
        <?php if ($user['role'] === 'admin'): ?>
          <a href="/admin/index.php" class="nav-link" style="color: var(--accent-gold); font-weight: 700;">Admin Management Portal</a>
        <?php endif; ?>
        <a href="/logout.php" class="nav-link" style="color: var(--danger);">Sign Out</a>
      <?php else: ?>
        <a href="/login.php" class="nav-link">Client Sign In</a>
        <a href="/register.php" class="nav-link">Register Patron Account</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Flash Notification Banner -->
  <?php if ($flash): ?>
    <div class="container" style="margin-top: 16px;">
      <div class="flash-alert <?php echo htmlspecialchars($flash['type']); ?>">
        <span><?php echo htmlspecialchars($flash['message']); ?></span>
      </div>
    </div>
  <?php endif; ?>
