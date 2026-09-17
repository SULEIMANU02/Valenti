<?php
/**
 * Valenti Atelier - Customer Dashboard & Order History (Light Mode & Responsive)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin('/login.php');

$pdo = getDB();
$userId = $_SESSION['user_id'];

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $postal = sanitize($_POST['postal_code'] ?? '');

    if ($name !== '') {
        $upd = $pdo->prepare("UPDATE users SET name = ?, phone = ?, address = ?, city = ?, postal_code = ? WHERE id = ?");
        $upd->execute([$name, $phone, $address, $city, $postal, $userId]);
        $_SESSION['user_name'] = $name;
        setFlash('success', 'Profile details updated successfully.');
        redirect('/account.php');
    }
}

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Fetch orders placed by this user OR by this email
$ordStmt = $pdo->prepare("
    SELECT * FROM orders 
    WHERE user_id = ? OR customer_email = ? 
    ORDER BY id DESC
");
$ordStmt->execute([$userId, $user['email']]);
$orders = $ordStmt->fetchAll();

$totalSpent = 0.0;
foreach ($orders as $o) {
    $totalSpent += (float)$o['total_amount'];
}

$pageTitle = "My Atelier Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 36px; padding-bottom: 80px;">
  
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
    <div>
      <span style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--accent-gold); display: block; margin-bottom: 4px; font-weight: 700;">Patron Member</span>
      <h1 style="font-size: clamp(1.8rem, 3.5vw, 2.2rem); color: var(--text-primary);"><?php echo htmlspecialchars($user['name']); ?></h1>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
      <?php if ($user['role'] === 'admin'): ?>
        <a href="/admin/index.php" class="btn btn-outline btn-sm" style="border-color: var(--accent-gold); color: var(--accent-gold); font-weight: 700;">
          Open Admin Console &rarr;
        </a>
      <?php endif; ?>
      <a href="/logout.php" class="btn btn-outline btn-sm">Sign Out</a>
    </div>
  </div>

  <!-- KPI Overview Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 36px;">
    
    <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 22px; box-shadow: var(--shadow-sm);">
      <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 6px; font-weight: 700;">Order Count</span>
      <div style="font-size: 1.8rem; font-weight: 700; color: var(--text-primary);"><?php echo count($orders); ?> Orders</div>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 22px; box-shadow: var(--shadow-sm);">
      <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 6px; font-weight: 700;">Total Wardrobe Value</span>
      <div style="font-size: 1.8rem; font-weight: 700; color: var(--accent-gold);"><?php echo formatPrice($totalSpent); ?></div>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 22px; box-shadow: var(--shadow-sm);">
      <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); display: block; margin-bottom: 6px; font-weight: 700;">Membership Tier</span>
      <div style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); font-family: var(--font-serif); margin-top: 4px;">Atelier VIP Circle</div>
    </div>

  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; align-items: start;">
    
    <!-- Order History Table -->
    <div class="cart-table-card">
      <h3 style="font-size: 1.2rem; color: var(--text-primary); margin-bottom: 18px;">Recent Wardrobe Orders</h3>

      <?php if (empty($orders)): ?>
        <p style="color: var(--text-muted); font-size: 0.9rem; padding: 20px 0;">You haven't placed any orders yet. Browse our current collection to acquire your first piece.</p>
        <a href="/shop.php" class="btn btn-primary btn-sm">Shop Collections</a>
      <?php else: ?>
        <div class="table-responsive">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Order Ref</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $ord): ?>
                <tr>
                  <td>
                    <strong style="color: var(--text-primary); font-size: 0.88rem;"><?php echo htmlspecialchars($ord['order_number']); ?></strong>
                  </td>
                  <td style="color: var(--text-muted); font-size: 0.82rem;">
                    <?php echo date('M d, Y', strtotime($ord['created_at'])); ?>
                  </td>
                  <td style="color: var(--accent-gold); font-weight: 700;">
                    <?php echo formatPrice((float)$ord['total_amount']); ?>
                  </td>
                  <td>
                    <span style="padding: 3px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; background: rgba(150, 111, 56, 0.1); color: var(--accent-gold); border: 1px solid rgba(150, 111, 56, 0.25);">
                      <?php echo htmlspecialchars($ord['order_status']); ?>
                    </span>
                  </td>
                  <td>
                    <a href="/order_confirmation.php?order=<?php echo urlencode($ord['order_number']); ?>" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">
                      Track / Receipt
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Edit Profile & Shipping Details Form -->
    <div class="cart-table-card">
      <h3 style="font-size: 1.2rem; color: var(--text-primary); margin-bottom: 18px;">Shipping &amp; Profile Details</h3>

      <form action="/account.php" method="POST">
        <input type="hidden" name="action" value="update_profile">

        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Email Address (Immutable)</label>
          <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="form-control" style="background: var(--bg-subtle); cursor: not-allowed;">
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Default Shipping Address</label>
          <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" class="form-control">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Postal Code</label>
            <input type="text" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" class="form-control">
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
      </form>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
