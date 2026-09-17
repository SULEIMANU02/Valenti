<?php
/**
 * Valenti Atelier - Admin Dashboard Overview (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$pdo = getDB();

// Handle quick order status update from dashboard
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'quick_status_update') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $newStatus = sanitize($_POST['order_status'] ?? '');
    if ($orderId > 0 && in_array($newStatus, ['Pending', 'Processing', 'Dispatched', 'Delivered', 'Cancelled'])) {
        $upd = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $upd->execute([$newStatus, $orderId]);
        setFlash('success', "Order #$orderId status updated to '$newStatus'.");
        redirect('/admin/index.php');
    }
}

// KPI 1: Total Revenue
$revStmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'Paid'");
$totalRevenue = (float)$revStmt->fetchColumn();

// KPI 2: Total Orders
$ordCountStmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
$totalOrders = (int)$ordCountStmt->fetchColumn();

// KPI 3: In Stock Pieces
$prodCountStmt = $pdo->query("SELECT COUNT(*) as count FROM products");
$totalProducts = (int)$prodCountStmt->fetchColumn();

// KPI 4: Customers
$userCountStmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$totalCustomers = (int)$userCountStmt->fetchColumn();

// Low Stock Alert (stock <= 10)
$lowStockStmt = $pdo->query("SELECT * FROM products WHERE stock <= 10 ORDER BY stock ASC LIMIT 5");
$lowStockItems = $lowStockStmt->fetchAll();

// Recent Orders
$recentOrdersStmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 6");
$recentOrders = $recentOrdersStmt->fetchAll();

$pageTitle = "Operations Dashboard";
require_once __DIR__ . '/admin_header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
  <div>
    <h1 style="font-size: clamp(1.8rem, 3vw, 2rem); color: var(--text-primary);">Operations &amp; Commerce Analytics</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Real-time performance metrics for Valenti Atelier.</p>
  </div>
  <div style="display: flex; gap: 12px;">
    <a href="/admin/product_form.php" class="btn btn-primary btn-sm">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      Add New Piece
    </a>
  </div>
</div>

<!-- KPI Cards -->
<div class="admin-kpi-grid">
  
  <div class="admin-card" style="margin-bottom: 0;">
    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 6px; font-weight: 700;">Gross Revenue</span>
    <div style="font-size: 1.8rem; font-weight: 700; color: var(--accent-gold);"><?php echo formatPrice($totalRevenue); ?></div>
    <span style="font-size: 0.75rem; color: var(--success); display: block; margin-top: 4px; font-weight: 600;">&uarr; B2C Inflow Settled</span>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 6px; font-weight: 700;">Total Orders</span>
    <div style="font-size: 1.8rem; font-weight: 700; color: var(--text-primary);"><?php echo $totalOrders; ?> Orders</div>
    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 4px;">100% Verified</span>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 6px; font-weight: 700;">Active Garment Styles</span>
    <div style="font-size: 1.8rem; font-weight: 700; color: var(--text-primary);"><?php echo $totalProducts; ?> Styles</div>
    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 4px;">Across 6 Departments</span>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 6px; font-weight: 700;">Registered Patrons</span>
    <div style="font-size: 1.8rem; font-weight: 700; color: var(--text-primary);"><?php echo $totalCustomers; ?> Clients</div>
    <span style="font-size: 0.75rem; color: var(--info); display: block; margin-top: 4px; font-weight: 600;">Atelier VIP Circle</span>
  </div>

</div>

<!-- Low Stock Alerts -->
<?php if (!empty($lowStockItems)): ?>
  <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-md); padding: 18px 20px; margin-bottom: 28px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <strong style="color: #b45309; font-size: 0.95rem;">Inventory Notice: Low Stock Threshold (&le; 10 units)</strong>
      </div>
      <a href="/admin/products.php" style="font-size: 0.8rem; color: #b45309; text-decoration: underline; font-weight: 700;">Manage Inventory &rarr;</a>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
      <?php foreach ($lowStockItems as $low): ?>
        <div style="background: #ffffff; border: 1px solid #fde68a; padding: 6px 12px; border-radius: 4px; font-size: 0.82rem;">
          <span style="color: var(--text-primary); font-weight: 600;"><?php echo htmlspecialchars($low['name']); ?></span> &bull; 
          <strong style="color: #d97706;"><?php echo $low['stock']; ?> left</strong>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<!-- Recent Orders Section -->
<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
    <h3 style="font-size: 1.25rem; color: var(--text-primary);">Recent Client Acquisitions</h3>
    <a href="/admin/orders.php" style="font-size: 0.82rem; color: var(--accent-gold); text-decoration: underline; font-weight: 700;">View All Orders &rarr;</a>
  </div>

  <div class="table-responsive">
    <table class="cart-table">
      <thead>
        <tr>
          <th>Order Number</th>
          <th>Client</th>
          <th>Total Amount</th>
          <th>Payment</th>
          <th>Logistics Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentOrders as $ord): ?>
          <tr>
            <td>
              <a href="/admin/order_detail.php?id=<?php echo $ord['id']; ?>" style="font-weight: 700; color: var(--text-primary);">
                <?php echo htmlspecialchars($ord['order_number']); ?>
              </a>
              <div style="font-size: 0.72rem; color: var(--text-muted);"><?php echo date('M d, Y H:i', strtotime($ord['created_at'])); ?></div>
            </td>
            <td>
              <div style="font-size: 0.88rem; color: var(--text-primary); font-weight: 600;"><?php echo htmlspecialchars($ord['customer_name']); ?></div>
              <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($ord['customer_email']); ?></div>
            </td>
            <td style="color: var(--accent-gold); font-weight: 700;">
              <?php echo formatPrice((float)$ord['total_amount']); ?>
            </td>
            <td>
              <span style="padding: 2px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 700; background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0;">
                <?php echo htmlspecialchars($ord['payment_status']); ?>
              </span>
            </td>
            <td>
              <form action="/admin/index.php" method="POST" style="display: inline-flex; align-items: center; gap: 6px;">
                <input type="hidden" name="action" value="quick_status_update">
                <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                <select name="order_status" class="sort-select" style="padding: 4px 8px; font-size: 0.75rem;" onchange="this.form.submit()">
                  <option value="Pending" <?php echo ($ord['order_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Processing" <?php echo ($ord['order_status'] === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                  <option value="Dispatched" <?php echo ($ord['order_status'] === 'Dispatched') ? 'selected' : ''; ?>>Dispatched</option>
                  <option value="Delivered" <?php echo ($ord['order_status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                  <option value="Cancelled" <?php echo ($ord['order_status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
              </form>
            </td>
            <td>
              <a href="/admin/order_detail.php?id=<?php echo $ord['id']; ?>" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">
                Inspect &rarr;
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
