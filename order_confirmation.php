<?php
/**
 * Valenti Atelier - Order Confirmation & Invoice Tracker (Light Mode & Responsive)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();
$orderNumber = trim($_GET['order'] ?? '');

if ($orderNumber === '') {
    redirect('/index.php');
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('/index.php');
}

// Fetch order items
$itemStmt = $pdo->prepare("
    SELECT oi.*, p.image, p.slug 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll();

// Determine tracking step index
$statuses = ['Pending', 'Processing', 'Dispatched', 'Delivered'];
$currentStatus = $order['order_status'];
$currentIndex = array_search($currentStatus, $statuses);
if ($currentIndex === false) $currentIndex = 1;

$pageTitle = "Order Confirmed • " . $order['order_number'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 80px; max-width: 960px;">
  
  <!-- Success Header -->
  <div style="text-align: center; margin-bottom: 36px;">
    <div style="width: 68px; height: 68px; border-radius: 50%; background: #ecfdf5; border: 2px solid var(--success); color: var(--success); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
      <svg width="34" height="34" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>
    <span style="font-size: 0.78rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-gold); display: block; margin-bottom: 6px; font-weight: 700;">
      Transaction Verified
    </span>
    <h1 style="font-size: clamp(1.8rem, 3.5vw, 2.5rem); color: var(--text-primary); margin-bottom: 8px;">Thank You For Your Patronage</h1>
    <p style="color: var(--text-secondary); font-size: 1.05rem;">
      Your order <strong><?php echo htmlspecialchars($order['order_number']); ?></strong> has been received by our atelier.
    </p>
  </div>

  <!-- Visual Order Tracking Timeline -->
  <div class="cart-table-card" style="margin-bottom: 28px;">
    <h3 style="font-size: 1.15rem; color: var(--text-primary); margin-bottom: 18px;">Live Garment Dispatch Status</h3>
    
    <div class="order-tracking-bar">
      
      <div class="tracking-step <?php echo ($currentIndex >= 0) ? 'completed' : ''; ?>">
        <div class="tracking-step-circle">1</div>
        <div>
          <span class="tracking-step-label">Order Placed</span>
          <span style="font-size: 0.75rem; color: var(--text-muted); display: block;"><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></span>
        </div>
      </div>

      <div class="tracking-step <?php echo ($currentIndex > 1) ? 'completed' : (($currentIndex === 1) ? 'active' : ''); ?>">
        <div class="tracking-step-circle">2</div>
        <div>
          <span class="tracking-step-label">Quality Check &amp; Boxing</span>
          <span style="font-size: 0.75rem; color: var(--text-muted); display: block;"><?php echo ($currentIndex >= 1) ? 'In Progress' : 'Pending'; ?></span>
        </div>
      </div>

      <div class="tracking-step <?php echo ($currentIndex > 2) ? 'completed' : (($currentIndex === 2) ? 'active' : ''); ?>">
        <div class="tracking-step-circle">3</div>
        <div>
          <span class="tracking-step-label">Courier Dispatched</span>
          <span style="font-size: 0.75rem; color: var(--text-muted); display: block;"><?php echo ($currentIndex >= 2) ? 'Active' : 'Awaiting'; ?></span>
        </div>
      </div>

      <div class="tracking-step <?php echo ($currentIndex === 3) ? 'completed' : ''; ?>">
        <div class="tracking-step-circle">4</div>
        <div>
          <span class="tracking-step-label">Doorstep Delivery</span>
          <span style="font-size: 0.75rem; color: var(--text-muted); display: block;"><?php echo ($currentIndex === 3) ? 'Completed' : 'Estimated 2-3 Days'; ?></span>
        </div>
      </div>

    </div>
  </div>

  <!-- Invoice & Order Summary Card -->
  <div class="cart-table-card" id="printableInvoice">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-subtle); padding-bottom: 20px; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
      <div>
        <div style="font-size: 1.4rem; font-family: var(--font-serif); font-weight: 700; color: var(--text-primary);"><?php echo BRAND_NAME; ?></div>
        <div style="font-size: 0.78rem; color: var(--accent-gold); font-weight: 700;">OFFICIAL ATELIER INVOICE &bull; SEN 803</div>
      </div>
      <div style="text-align: right;">
        <div style="font-weight: 700; color: var(--text-primary); font-size: 1rem;">Invoice #<?php echo htmlspecialchars($order['order_number']); ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Placed: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
        <div style="display: inline-block; margin-top: 6px; padding: 3px 10px; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700; background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0;">
          Payment: <?php echo htmlspecialchars($order['payment_status']); ?>
        </div>
      </div>
    </div>

    <!-- Client & Delivery Metadata -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 24px; font-size: 0.88rem;">
      <div>
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 4px; font-weight: 700;">Billed &amp; Shipped To:</span>
        <strong style="color: var(--text-primary); display: block; font-size: 1rem;"><?php echo htmlspecialchars($order['customer_name']); ?></strong>
        <div style="color: var(--text-secondary); margin-top: 4px;">
          <?php echo htmlspecialchars($order['shipping_address']); ?><br>
          <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_postal']); ?><br>
          <?php echo htmlspecialchars($order['shipping_country']); ?>
        </div>
        <div style="color: var(--text-muted); margin-top: 6px;">
          Email: <?php echo htmlspecialchars($order['customer_email']); ?><br>
          Phone: <?php echo htmlspecialchars($order['customer_phone']); ?>
        </div>
      </div>

      <div>
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 4px; font-weight: 700;">Payment &amp; Logistics:</span>
        <div style="color: var(--text-secondary); margin-bottom: 6px;">
          Method: <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($order['payment_method']); ?></strong>
        </div>
        <div style="color: var(--text-secondary); margin-bottom: 6px;">
          Status: <strong style="color: var(--accent-gold);"><?php echo htmlspecialchars($order['order_status']); ?></strong>
        </div>
        <?php if (!empty($order['notes'])): ?>
          <div style="color: var(--text-muted); font-size: 0.82rem; margin-top: 8px; background: var(--bg-subtle); padding: 8px 12px; border-radius: 4px;">
            Special Notes: <?php echo htmlspecialchars($order['notes']); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Items Table with Responsive Wrapper -->
    <div class="table-responsive">
      <table class="cart-table" style="margin-bottom: 20px;">
        <thead>
          <tr>
            <th>Ordered Piece</th>
            <th>Size &amp; Color</th>
            <th>Price</th>
            <th>Qty</th>
            <th style="text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td>
                <div class="cart-item-flex">
                  <?php if (!empty($it['image'])): ?>
                    <img src="/<?php echo htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['product_name']); ?>" style="width: 44px; height: 55px; object-fit: cover; border-radius: 4px;">
                  <?php endif; ?>
                  <strong style="color: var(--text-primary); font-size: 0.95rem;"><?php echo htmlspecialchars($it['product_name']); ?></strong>
                </div>
              </td>
              <td style="color: var(--text-secondary); font-size: 0.85rem;">
                Size: <?php echo htmlspecialchars($it['size'] ?? 'Standard'); ?><br>
                Color: <?php echo htmlspecialchars($it['color'] ?? 'Default'); ?>
              </td>
              <td style="color: var(--accent-gold); font-weight: 600;"><?php echo formatPrice((float)$it['price']); ?></td>
              <td style="color: var(--text-primary); font-weight: 600;"><?php echo $it['quantity']; ?></td>
              <td style="text-align: right; color: var(--text-primary); font-weight: 700;"><?php echo formatPrice((float)$it['total']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Totals Breakdown -->
    <div style="max-width: 320px; margin-left: auto; display: flex; flex-direction: column; gap: 8px; font-size: 0.92rem;">
      <div class="summary-row">
        <span>Subtotal</span>
        <span><?php echo formatPrice((float)$order['subtotal']); ?></span>
      </div>

      <?php if ((float)$order['discount_amount'] > 0): ?>
        <div class="summary-row" style="color: var(--success); font-weight: 600;">
          <span>Voucher (<?php echo htmlspecialchars($order['coupon_code'] ?? 'Discount'); ?>)</span>
          <span>&minus;<?php echo formatPrice((float)$order['discount_amount']); ?></span>
        </div>
      <?php endif; ?>

      <div class="summary-row">
        <span>Shipping</span>
        <span><?php echo ((float)$order['shipping_fee'] == 0.0) ? '<strong style="color:var(--success)">COMPLIMENTARY</strong>' : formatPrice((float)$order['shipping_fee']); ?></span>
      </div>

      <div class="summary-row">
        <span>Tax (7.5%)</span>
        <span><?php echo formatPrice((float)$order['tax_amount']); ?></span>
      </div>

      <div class="summary-row summary-total">
        <span>Amount Settled</span>
        <span style="color: var(--accent-gold);"><?php echo formatPrice((float)$order['total_amount']); ?></span>
      </div>
    </div>

  </div>

  <!-- Action CTAs -->
  <div style="display: flex; gap: 14px; justify-content: center; margin-top: 28px; flex-wrap: wrap;">
    <button onclick="window.print()" class="btn btn-outline">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <polyline points="6 9 6 2 18 2 18 9"></polyline>
        <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"></path>
        <rect x="6" y="14" width="12" height="8"></rect>
      </svg>
      Print Receipt
    </button>

    <a href="/account.php" class="btn btn-outline">View In My Orders</a>
    <a href="/shop.php" class="btn btn-primary">Continue Shopping &rarr;</a>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
