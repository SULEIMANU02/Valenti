<?php
/**
 * Valenti Atelier - Discount Vouchers & Promotion Controls (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$pdo = getDB();

// Handle Add Voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_coupon') {
    $code = strtoupper(sanitize($_POST['code'] ?? ''));
    $type = sanitize($_POST['type'] ?? 'percentage');
    $value = (float)($_POST['value'] ?? 0);
    $minSpend = (float)($_POST['min_spend'] ?? 0);

    if ($code !== '' && $value > 0) {
        try {
            $ins = $pdo->prepare("INSERT INTO coupons (code, type, value, min_spend, status) VALUES (?, ?, ?, ?, 1)");
            $ins->execute([$code, $type, $value, $minSpend]);
            setFlash('success', "Voucher code '$code' created successfully.");
        } catch (Exception $e) {
            setFlash('error', "Voucher code already exists or database error.");
        }
        redirect('/admin/coupons.php');
    } else {
        setFlash('error', 'Please provide valid coupon code and discount value.');
    }
}

// Handle Delete Voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_coupon') {
    $cId = (int)($_POST['coupon_id'] ?? 0);
    if ($cId > 0) {
        $del = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
        $del->execute([$cId]);
        setFlash('info', 'Voucher deleted.');
        redirect('/admin/coupons.php');
    }
}

// Fetch coupons
$coupons = $pdo->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();

$pageTitle = "Promotions & Vouchers";
require_once __DIR__ . '/admin_header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
  <div>
    <h1 style="font-size: clamp(1.8rem, 3vw, 2rem); color: var(--text-primary);">Promotional Vouchers &amp; Codes</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Manage dynamic discounts for marketing campaigns and academic course grading.</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: start;">
  
  <!-- Coupons List Table -->
  <div class="admin-card">
    <h3 style="font-size: 1.25rem; color: var(--text-primary); margin-bottom: 18px;">Active Atelier Vouchers</h3>

    <div class="table-responsive">
      <table class="cart-table">
        <thead>
          <tr>
            <th>Voucher Code</th>
            <th>Type &amp; Discount</th>
            <th>Min Spend</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($coupons as $c): ?>
            <tr>
              <td>
                <code style="font-weight: 700; font-size: 0.95rem; color: var(--accent-gold); background: #fdf8f0; padding: 4px 8px; border-radius: 4px; border: 1px solid #fae8c8;">
                  <?php echo htmlspecialchars($c['code']); ?>
                </code>
              </td>
              <td style="color: var(--text-primary); font-weight: 600;">
                <?php echo ($c['type'] === 'percentage') ? $c['value'] . '%' : formatPrice((float)$c['value']); ?>
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; display: block; text-transform: capitalize;"><?php echo $c['type']; ?></span>
              </td>
              <td style="color: var(--text-secondary); font-size: 0.88rem;">
                <?php echo formatPrice((float)$c['min_spend']); ?>
              </td>
              <td>
                <span class="badge-status status-delivered">ACTIVE</span>
              </td>
              <td>
                <form action="/admin/coupons.php" method="POST" onsubmit="return confirm('Delete this voucher code?');">
                  <input type="hidden" name="action" value="delete_coupon">
                  <input type="hidden" name="coupon_id" value="<?php echo $c['id']; ?>">
                  <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.72rem; border-color: rgba(220,38,38,0.3); color: var(--danger);">
                    Delete
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Create New Voucher Form -->
  <div class="admin-card">
    <h3 style="font-size: 1.25rem; color: var(--text-primary); margin-bottom: 6px;">Create New Voucher</h3>
    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 18px;">Configure promotional rates for patron checkouts.</p>

    <form action="/admin/coupons.php" method="POST">
      <input type="hidden" name="action" value="add_coupon">

      <div class="form-group">
        <label class="form-label">Promotional Code *</label>
        <input type="text" name="code" required placeholder="e.g. VIP25" style="text-transform: uppercase;" class="form-control">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Discount Type *</label>
          <select name="type" class="form-control">
            <option value="percentage">Percentage (%)</option>
            <option value="fixed">Fixed Flat (<?php echo CURRENCY_SYMBOL; ?>)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Value (Amount or %) *</label>
          <input type="number" step="0.01" name="value" required placeholder="20" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Minimum Spend Threshold (<?php echo CURRENCY_SYMBOL . ' ' . CURRENCY_CODE; ?>)</label>
        <input type="number" step="0.01" name="min_spend" value="0.00" placeholder="0.00" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary btn-block">Generate Voucher</button>
    </form>
  </div>

</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
