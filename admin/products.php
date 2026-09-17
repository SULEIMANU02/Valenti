<?php
/**
 * Valenti Atelier - Product Catalog Management (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$pdo = getDB();

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $prodId = (int)($_POST['product_id'] ?? 0);
    if ($prodId > 0) {
        $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $del->execute([$prodId]);
        setFlash('info', 'Garment piece has been removed from catalog.');
        redirect('/admin/products.php');
    }
}

// Fetch products with category names
$stmt = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll();

$pageTitle = "Inventory Management";
require_once __DIR__ . '/admin_header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
  <div>
    <h1 style="font-size: clamp(1.8rem, 3vw, 2rem); color: var(--text-primary);">Garment Inventory &amp; Styles</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Catalog of all current couture &amp; streetwear pieces (<?php echo count($products); ?> total).</p>
  </div>
  <a href="/admin/product_form.php" class="btn btn-primary btn-sm">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Create New Garment
  </a>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="cart-table">
      <thead>
        <tr>
          <th>Piece Preview</th>
          <th>Category &amp; Dept</th>
          <th>Pricing</th>
          <th>Stock Units</th>
          <th>Badge / Focus</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td>
              <div class="cart-item-flex">
                <img src="/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" style="width: 48px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-subtle); background: #f5f3ef;">
                <div>
                  <strong style="color: var(--text-primary); font-size: 0.92rem;"><?php echo htmlspecialchars($p['name']); ?></strong>
                  <div style="font-size: 0.72rem; color: var(--text-muted);">SKU: VLT-<?php echo str_pad($p['id'], 4, '0', STR_PAD_LEFT); ?></div>
                </div>
              </div>
            </td>
            <td>
              <div style="color: var(--text-primary); font-size: 0.88rem; font-weight: 600;"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></div>
              <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($p['gender']); ?></div>
            </td>
            <td>
              <div style="color: var(--accent-gold); font-weight: 700;">
                <?php echo formatPrice((float)$p['price']); ?>
              </div>
              <?php if ($p['sale_price'] !== null && $p['sale_price'] > 0): ?>
                <div style="font-size: 0.75rem; color: var(--danger); font-weight: 600;">Sale: <?php echo formatPrice((float)$p['sale_price']); ?></div>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($p['stock'] <= 10): ?>
                <span style="color: var(--warning); font-weight: 700;"><?php echo $p['stock']; ?> (Low)</span>
              <?php else: ?>
                <span style="color: var(--success); font-weight: 600;"><?php echo $p['stock']; ?> units</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($p['badge'])): ?>
                <span class="badge-status status-dispatched"><?php echo htmlspecialchars($p['badge']); ?></span>
              <?php endif; ?>
              <?php if ($p['featured']): ?>
                <span class="badge-status status-delivered">FEATURED</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display: flex; gap: 8px;">
                <a href="/admin/product_form.php?id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">
                  Edit
                </a>
                <form action="/admin/products.php" method="POST" onsubmit="return confirm('Delete this garment permanently from inventory?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                  <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.75rem; border-color: rgba(220,38,38,0.3); color: var(--danger);">
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
