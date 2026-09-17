<?php
/**
 * Valenti Atelier - Shopping Cart & Coupon Engine (Light Mode & Responsive)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $size = sanitize($_POST['size'] ?? 'M');
        $color = sanitize($_POST['color'] ?? 'Default');

        if ($productId > 0) {
            addToCart($productId, $qty, $size, $color);
            setFlash('success', 'Piece added to your shopping bag.');
        }
        redirect('/cart.php');
    }

    if ($action === 'update') {
        $cartKey = sanitize($_POST['cart_key'] ?? '');
        $qty = (int)($_POST['quantity'] ?? 1);
        updateCart($cartKey, $qty);
        setFlash('success', 'Bag updated.');
        redirect('/cart.php');
    }

    if ($action === 'remove') {
        $cartKey = sanitize($_POST['cart_key'] ?? '');
        removeFromCart($cartKey);
        setFlash('info', 'Piece removed from shopping bag.');
        redirect('/cart.php');
    }

    if ($action === 'apply_coupon') {
        $code = strtoupper(sanitize($_POST['coupon_code'] ?? ''));
        if ($code !== '') {
            $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1");
            $stmt->execute([$code]);
            $coupon = $stmt->fetch();

            if ($coupon) {
                $_SESSION['applied_coupon'] = $coupon;
                setFlash('success', "Coupon '{$coupon['code']}' applied successfully!");
            } else {
                setFlash('error', 'Invalid or expired promotional voucher code.');
            }
        }
        redirect('/cart.php');
    }

    if ($action === 'remove_coupon') {
        unset($_SESSION['applied_coupon']);
        setFlash('info', 'Promotional code removed.');
        redirect('/cart.php');
    }

    if ($action === 'clear') {
        clearCart();
        setFlash('info', 'Shopping bag emptied.');
        redirect('/cart.php');
    }
}

$cartData = calculateCartTotals($pdo);
$pageTitle = "Shopping Bag";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 36px; padding-bottom: 80px;">
  
  <div style="margin-bottom: 24px;">
    <h1 style="font-size: clamp(1.8rem, 3vw, 2.2rem); color: var(--text-primary);">Your Shopping Bag</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Review your selected pieces before proceeding to secure bespoke checkout.</p>
  </div>

  <?php if (empty($cartData['items'])): ?>
    <div style="background: var(--bg-surface); border: 1px dashed var(--border-medium); border-radius: var(--radius-md); padding: 70px 20px; text-align: center;">
      <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--text-muted); margin: 0 auto 16px auto;">
        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <path d="M16 10a4 4 0 01-8 0"></path>
      </svg>
      <h3 style="font-size: 1.4rem; color: var(--text-primary); margin-bottom: 12px;">Your Bag is Currently Empty</h3>
      <p style="color: var(--text-secondary); margin-bottom: 24px;">Discover contemporary tailoring and luxury knitwear in our latest collection.</p>
      <a href="/shop.php" class="btn btn-primary">Explore Catalog</a>
    </div>
  <?php else: ?>

    <div class="cart-grid">
      
      <!-- Cart Table Card with Responsive Scroll -->
      <div class="cart-table-card">
        <div class="table-responsive">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Piece</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cartData['items'] as $item): ?>
                <tr>
                  <td>
                    <div class="cart-item-flex">
                      <div class="cart-thumb">
                        <img src="/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                      </div>
                      <div>
                        <a href="/product.php?id=<?php echo $item['product_id']; ?>" style="font-weight: 700; color: var(--text-primary); display: block; margin-bottom: 3px;">
                          <?php echo htmlspecialchars($item['name']); ?>
                        </a>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">
                          Size: <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($item['size']); ?></strong> &bull; 
                          Color: <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($item['color']); ?></strong>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td style="color: var(--accent-gold); font-weight: 700;">
                    <?php echo formatPrice($item['price']); ?>
                  </td>
                  <td>
                    <form action="/cart.php" method="POST" class="cart-qty-form">
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($item['key']); ?>">
                      <div class="qty-control">
                        <button type="button" class="qty-btn qty-dec">&minus;</button>
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['max_stock']; ?>" class="qty-input">
                        <button type="button" class="qty-btn qty-inc">&plus;</button>
                      </div>
                    </form>
                  </td>
                  <td style="font-weight: 700; color: var(--text-primary);">
                    <?php echo formatPrice($item['total']); ?>
                  </td>
                  <td>
                    <form action="/cart.php" method="POST">
                      <input type="hidden" name="action" value="remove">
                      <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($item['key']); ?>">
                      <button type="submit" class="icon-btn" title="Remove Item" style="color: var(--text-muted);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <polyline points="3 6 5 6 21 6"></polyline>
                          <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                        </svg>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 12px;">
          <a href="/shop.php" class="btn btn-outline btn-sm">&larr; Continue Shopping</a>
          <form action="/cart.php" method="POST">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Clear entire shopping bag?')">Clear Bag</button>
          </form>
        </div>
      </div>

      <!-- Order Summary Card -->
      <div class="cart-summary-card">
        <h3 style="font-size: 1.25rem; color: var(--text-primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 14px;">Order Summary</h3>

        <div class="summary-row">
          <span>Bag Subtotal</span>
          <span><?php echo formatPrice($cartData['subtotal']); ?></span>
        </div>

        <?php if ($cartData['discount'] > 0): ?>
          <div class="summary-row" style="color: var(--success); font-weight: 600;">
            <span>Voucher (<?php echo htmlspecialchars($cartData['coupon_code']); ?>)</span>
            <span>&minus;<?php echo formatPrice($cartData['discount']); ?></span>
          </div>
          <form action="/cart.php" method="POST" style="margin-bottom: 14px; text-align: right;">
            <input type="hidden" name="action" value="remove_coupon">
            <button type="submit" style="background: none; border: none; font-size: 0.75rem; color: var(--danger); cursor: pointer; text-decoration: underline;">
              Remove Voucher
            </button>
          </form>
        <?php endif; ?>

        <div class="summary-row">
          <span>Estimated Shipping</span>
          <span>
            <?php if ($cartData['shipping'] == 0.0): ?>
              <span style="color: var(--success); font-weight: 700;">COMPLIMENTARY</span>
            <?php else: ?>
              <?php echo formatPrice($cartData['shipping']); ?>
            <?php endif; ?>
          </span>
        </div>

        <div class="summary-row">
          <span>Estimated Tax (7.5%)</span>
          <span><?php echo formatPrice($cartData['tax']); ?></span>
        </div>

        <div class="summary-row summary-total">
          <span>Total</span>
          <span style="color: var(--accent-gold);"><?php echo formatPrice($cartData['total']); ?></span>
        </div>

        <!-- Coupon Form -->
        <div style="margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--border-subtle);">
          <span style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 8px; font-weight: 700;">
            Promotional Voucher
          </span>
          <form action="/cart.php" method="POST" class="coupon-form">
            <input type="hidden" name="action" value="apply_coupon">
            <input type="text" name="coupon_code" placeholder="e.g. SEN803" class="coupon-input" required value="<?php echo htmlspecialchars($cartData['coupon_code'] ?? ''); ?>">
            <button type="submit" class="btn btn-outline btn-sm">Apply</button>
          </form>
          <div style="font-size: 0.75rem; color: var(--text-muted);">
            Available test codes: <code style="color: var(--accent-gold); font-weight: 700;">SEN803</code> (20% off), <code style="color: var(--accent-gold); font-weight: 700;">WELCOME10</code> (10% off).
          </div>
        </div>

        <div style="margin-top: 24px;">
          <a href="/checkout.php" class="btn btn-primary btn-block" style="height: 48px;">
            Proceed To Checkout &rarr;
          </a>
        </div>

        <div style="margin-top: 16px; text-align: center; font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0110 0v4"></path>
          </svg>
          256-Bit SSL Encrypted Checkout Simulation
        </div>

      </div>

    </div>

  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
