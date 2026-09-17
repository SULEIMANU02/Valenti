<?php
/**
 * Valenti Atelier - Orders Fulfillment & Logistics Console (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$pdo = getDB();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = sanitize($_POST['order_status'] ?? '');

    if ($orderId > 0 && in_array($status, ['Pending', 'Processing', 'Dispatched', 'Delivered', 'Cancelled'])) {
        $upd = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $upd->execute([$status, $orderId]);
        setFlash('success', "Order #$orderId fulfillment status updated to '$status'.");
        redirect('/admin/orders.php');
    }
}

// Filter by status
$filterStatus = trim($_GET['status'] ?? '');
$sql = "SELECT * FROM orders WHERE 1=1";
$params = [];

if ($filterStatus !== '') {
    $sql .= " AND order_status = ?";
    $params[] = $filterStatus;
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$pageTitle = "Orders Fulfillment";
require_once __DIR__ . '/admin_header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
  <div>
    <h1 style="font-size: clamp(1.8rem, 3vw, 2rem); color: var(--text-primary);">Orders &amp; Garment Logistics</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Track, inspect, and fulfill client garment shipments.</p>
  </div>
  
  <!-- Status Filter Pills -->
  <div style="display: flex; gap: 8px; font-size: 0.82rem; flex-wrap: wrap;">
    <a href="/admin/orders.php" class="btn btn-sm <?php echo empty($filterStatus) ? 'btn-primary' : 'btn-outline'; ?>">All Orders</a>
    <a href="/admin/orders.php?status=Processing" class="btn btn-sm <?php echo ($filterStatus === 'Processing') ? 'btn-primary' : 'btn-outline'; ?>">Processing</a>
    <a href="/admin/orders.php?status=Dispatched" class="btn btn-sm <?php echo ($filterStatus === 'Dispatched') ? 'btn-primary' : 'btn-outline'; ?>">Dispatched</a>
    <a href="/admin/orders.php?status=Delivered" class="btn btn-sm <?php echo ($filterStatus === 'Delivered') ? 'btn-primary' : 'btn-outline'; ?>">Delivered</a>
  </div>
</div>

<div class="admin-card">
  <?php if (empty($orders)): ?>
    <p style="text-align: center; color: var(--text-muted); padding: 40px 0;">No client orders match the current status filter.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="cart-table">
        <thead>
          <tr>
            <th>Order Number</th>
            <th>Patron Client</th>
            <th>Destination</th>
            <th>Settlement</th>
            <th>Total</th>
            <th>Dispatch Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $ord): ?>
            <tr>
              <td>
                <a href="/admin/order_detail.php?id=<?php echo $ord['id']; ?>" style="font-weight: 700; color: var(--text-primary);">
                  <?php echo htmlspecialchars($ord['order_number']); ?>
                </a>
                <div style="font-size: 0.72rem; color: var(--text-muted);"><?php echo date('M d, Y H:i', strtotime($ord['created_at'])); ?></div>
              </td>
              <td>
                <div style="color: var(--text-primary); font-size: 0.88rem; font-weight: 600;"><?php echo htmlspecialchars($ord['customer_name']); ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($ord['customer_email']); ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($ord['customer_phone']); ?></div>
              </td>
              <td style="font-size: 0.82rem; color: var(--text-secondary);">
                <?php echo htmlspecialchars($ord['shipping_city']); ?>, <?php echo htmlspecialchars($ord['shipping_country']); ?>
              </td>
              <td>
                <div style="font-size: 0.82rem; color: var(--text-primary);"><?php echo htmlspecialchars($ord['payment_method']); ?></div>
                <span style="display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0;">
                  <?php echo htmlspecialchars($ord['payment_status']); ?>
                </span>
              </td>
              <td style="color: var(--accent-gold); font-weight: 700; font-size: 1rem;">
                <?php echo formatPrice((float)$ord['total_amount']); ?>
              </td>
              <td>
                <form action="/admin/orders.php" method="POST" style="display: flex; gap: 6px; align-items: center;">
                  <input type="hidden" name="action" value="update_status">
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
                  Packing Slip &rarr;
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
