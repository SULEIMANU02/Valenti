<?php
/**
 * Valenti Atelier - Patrons & Clients Directory (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$pdo = getDB();

// Fetch customers with total orders and total spent
$stmt = $pdo->query("
    SELECT u.*, 
           COUNT(o.id) as order_count,
           COALESCE(SUM(o.total_amount), 0) as total_spent
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    WHERE u.role = 'customer'
    GROUP BY u.id
    ORDER BY u.id DESC
");
$customers = $stmt->fetchAll();

$pageTitle = "Patrons Directory";
require_once __DIR__ . '/admin_header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
  <div>
    <h1 style="font-size: clamp(1.8rem, 3vw, 2rem); color: var(--text-primary);">Patrons &amp; Client Registry</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Registered consumers engaging with Valenti Atelier B2C commerce.</p>
  </div>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="cart-table">
      <thead>
        <tr>
          <th>Patron Name</th>
          <th>Contact Coordinates</th>
          <th>Delivery City</th>
          <th>Orders Placed</th>
          <th>Total Value Spent</th>
          <th>Registered</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($customers as $c): ?>
          <tr>
            <td>
              <div style="font-weight: 700; color: var(--text-primary); font-size: 0.95rem;"><?php echo htmlspecialchars($c['name']); ?></div>
              <span style="font-size: 0.72rem; color: var(--accent-gold); letter-spacing: 0.1em; text-transform: uppercase; font-weight: 600;">Atelier Member</span>
            </td>
            <td>
              <div style="color: var(--text-primary); font-size: 0.88rem; font-weight: 600;"><?php echo htmlspecialchars($c['email']); ?></div>
              <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($c['phone'] ?? 'No phone recorded'); ?></div>
            </td>
            <td style="color: var(--text-secondary); font-size: 0.88rem;">
              <?php echo htmlspecialchars($c['city'] ?? 'Unspecified'); ?>
            </td>
            <td>
              <strong style="color: var(--text-primary);"><?php echo $c['order_count']; ?> Orders</strong>
            </td>
            <td style="color: var(--accent-gold); font-weight: 700;">
              <?php echo formatPrice((float)$c['total_spent']); ?>
            </td>
            <td style="color: var(--text-muted); font-size: 0.82rem;">
              <?php echo date('M d, Y', strtotime($c['created_at'])); ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
