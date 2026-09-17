<?php
/**
 * Valenti Atelier - Admin Order Detail & Packing Slip (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$pdo = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect('/admin/orders.php');
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $status = sanitize($_POST['order_status'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    $upd = $pdo->prepare("UPDATE orders SET order_status = ?, notes = ? WHERE id = ?");
    $upd->execute([$status, $notes, $id]);
    setFlash('success', 'Order status and notes updated.');
    redirect('/admin/order_detail.php?id=' . $id);
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('/admin/orders.php');
}

// Fetch items
$itemStmt = $pdo->prepare("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

$pageTitle = "Order " . $order['order_number'];
require_once __DIR__ . '/admin_header.php';
?>

<div style="max-width: 960px; margin: 0 auto;">
  
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
    <div>
      <a href="/admin/orders.php" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent-gold); display: inline-block; margin-bottom: 4px; font-weight: 700;">
        &larr; Back to All Orders
      </a>
      <h1 style="font-size: clamp(1.8rem, 3vw, 2rem); color: var(--text-primary);">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
    </div>
    <button onclick="window.print()" class="btn btn-outline btn-sm">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
      Print Packing Slip
    </button>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: start;">
    
    <!-- Items & Calculation -->
    <div class="admin-card">
      <h3 style="font-size: 1.15rem; color: var(--text-primary); margin-bottom: 16px;">Garment Line Items</h3>
      
      <div class="table-responsive">
        <table class="cart-table" style="margin-bottom: 18px;">
          <thead>
            <tr>
              <th>Piece</th>
              <th>Specs</th>
              <th>Price</th>
              <th>Qty</th>
              <th style="text-align: right;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td>
                  <strong style="color: var(--text-primary); font-size: 0.9rem;"><?php echo htmlspecialchars($it['product_name']); ?></strong>
                </td>
                <td style="font-size: 0.8rem; color: var(--text-muted);">
                  <?php echo htmlspecialchars($it['size'] ?? 'M'); ?> / <?php echo htmlspecialchars($it['color'] ?? 'Default'); ?>
                </td>
                <td style="color: var(--accent-gold); font-size: 0.85rem; font-weight: 600;"><?php echo formatPrice((float)$it['price']); ?></td>
                <td style="color: var(--text-primary); font-weight: 700;"><?php echo $it['quantity']; ?></td>
                <td style="text-align: right; color: var(--text-primary); font-weight: 700;"><?php echo formatPrice((float)$it['total']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Totals -->
      <div style="max-width: 300px; margin-left: auto; display: flex; flex-direction: column; gap: 8px; font-size: 0.88rem;">
        <div class="summary-row"><span>Subtotal</span><span><?php echo formatPrice((float)$order['subtotal']); ?></span></div>
        <?php if ((float)$order['discount_amount'] > 0): ?>
          <div class="summary-row" style="color: var(--success); font-weight: 600;"><span>Voucher (<?php echo htmlspecialchars($order['coupon_code']); ?>)</span><span>&minus;<?php echo formatPrice((float)$order['discount_amount']); ?></span></div>
        <?php endif; ?>
        <div class="summary-row"><span>Shipping</span><span><?php echo ((float)$order['shipping_fee'] == 0.0) ? '<strong style="color:var(--success)">COMPLIMENTARY</strong>' : formatPrice((float)$order['shipping_fee']); ?></span></div>
        <div class="summary-row"><span>Tax</span><span><?php echo formatPrice((float)$order['tax_amount']); ?></span></div>
        <div class="summary-row summary-total" style="font-size: 1.15rem;"><span>Total Paid</span><span style="color: var(--accent-gold);"><?php echo formatPrice((float)$order['total_amount']); ?></span></div>
      </div>
    </div>

    <!-- Fulfillment Management Form -->
    <div class="admin-card">
      <h3 style="font-size: 1.15rem; color: var(--text-primary); margin-bottom: 16px;">Update Logistics Status</h3>

      <form action="/admin/order_detail.php?id=<?php echo $order['id']; ?>" method="POST">
        <input type="hidden" name="action" value="update_order">

        <div class="form-group">
          <label class="form-label">Dispatch Status</label>
          <select name="order_status" class="form-control">
            <option value="Pending" <?php echo ($order['order_status'] === 'Pending') ? 'selected' : ''; ?>>Pending Inspection</option>
            <option value="Processing" <?php echo ($order['order_status'] === 'Processing') ? 'selected' : ''; ?>>Processing &amp; Tailoring</option>
            <option value="Dispatched" <?php echo ($order['order_status'] === 'Dispatched') ? 'selected' : ''; ?>>Dispatched (Handed to Courier)</option>
            <option value="Delivered" <?php echo ($order['order_status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered to Client</option>
            <option value="Cancelled" <?php echo ($order['order_status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled / Refunded</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Client / Courier Instructions</label>
          <textarea name="notes" rows="3" class="form-control"><?php echo htmlspecialchars($order['notes'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Save Status</button>
      </form>

      <div style="margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--border-subtle); font-size: 0.85rem;">
        <span style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 4px; font-weight: 700;">Client Details</span>
        <strong style="color: var(--text-primary); display: block; font-size: 0.95rem;"><?php echo htmlspecialchars($order['customer_name']); ?></strong>
        <div style="color: var(--text-secondary); margin-top: 4px;">
          <?php echo htmlspecialchars($order['customer_email']); ?><br>
          <?php echo htmlspecialchars($order['customer_phone']); ?>
        </div>
        <div style="color: var(--text-muted); margin-top: 6px;">
          <?php echo htmlspecialchars($order['shipping_address']); ?><br>
          <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_postal']); ?><br>
          <?php echo htmlspecialchars($order['shipping_country']); ?>
        </div>
      </div>

    </div>

  </div>

</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
