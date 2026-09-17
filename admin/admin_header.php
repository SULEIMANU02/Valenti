<?php
/**
 * Valenti Atelier - Admin Portal Header (Editorial Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireAdmin('/login.php');

$user = currentUser();
$flash = getFlash();
$activePage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . BRAND_NAME . ' Admin' : BRAND_NAME . ' Management Console'; ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    /* Admin Specific Styling (Editorial Light Mode) */
    .admin-layout {
      display: grid;
      grid-template-columns: 260px 1fr;
      min-height: 100vh;
      background: var(--bg-main);
    }
    .admin-sidebar {
      background: #ffffff;
      border-right: 1px solid var(--border-subtle);
      padding: 30px 20px;
      display: flex;
      flex-direction: column;
      box-shadow: var(--shadow-sm);
    }
    .admin-main {
      padding: 36px 40px;
      background: var(--bg-main);
      overflow-y: auto;
    }
    .admin-nav {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-top: 26px;
    }
    .admin-nav a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border-radius: var(--radius-sm);
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text-secondary);
      transition: all var(--transition-fast);
    }
    .admin-nav a:hover, .admin-nav a.active {
      background: rgba(150, 111, 56, 0.1);
      color: var(--accent-gold);
    }
    .admin-kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 32px;
    }
    .admin-card {
      background: #ffffff;
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      padding: 24px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
    }
    .badge-status {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }
    .status-processing { background: #eff6ff; color: #0284c7; border: 1px solid #bfdbfe; }
    .status-dispatched { background: #fdf8f0; color: var(--accent-gold); border: 1px solid #fae8c8; }
    .status-delivered { background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0; }
    .status-cancelled { background: #fef2f2; color: var(--danger); border: 1px solid #fecaca; }
  </style>
</head>
<body>

<div class="admin-layout">
  
  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div style="margin-bottom: 16px;">
      <a href="/admin/index.php" style="font-family: var(--font-serif); font-size: 1.25rem; font-weight: 700; color: var(--text-primary); letter-spacing: 0.12em; display: block;">
        <?php echo BRAND_NAME; ?>
      </a>
      <span style="font-size: 0.68rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-gold); font-weight: 700;">
        Operations &bull; SEN 803
      </span>
    </div>

    <ul class="admin-nav">
      <li>
        <a href="/admin/index.php" class="<?php echo ($activePage === 'index.php') ? 'active' : ''; ?>">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
          Dashboard
        </a>
      </li>
      <li>
        <a href="/admin/products.php" class="<?php echo in_array($activePage, ['products.php', 'product_form.php']) ? 'active' : ''; ?>">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
          Products &amp; Stock
        </a>
      </li>
      <li>
        <a href="/admin/orders.php" class="<?php echo in_array($activePage, ['orders.php', 'order_detail.php']) ? 'active' : ''; ?>">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
          Orders &amp; Dispatch
        </a>
      </li>
      <li>
        <a href="/admin/coupons.php" class="<?php echo ($activePage === 'coupons.php') ? 'active' : ''; ?>">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
          Discount Vouchers
        </a>
      </li>
      <li>
        <a href="/admin/customers.php" class="<?php echo ($activePage === 'customers.php') ? 'active' : ''; ?>">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
          Patrons &amp; Clients
        </a>
      </li>
    </ul>

    <div style="margin-top: auto; padding-top: 24px; border-top: 1px solid var(--border-subtle); display: flex; flex-direction: column; gap: 8px;">
      <a href="/index.php" target="_blank" class="btn btn-outline btn-sm btn-block" style="font-size: 0.75rem;">
        View Live Storefront &rarr;
      </a>
      <a href="/logout.php" class="btn btn-outline btn-sm btn-block" style="font-size: 0.75rem; border-color: rgba(220,38,38,0.3); color: #dc2626;">
        Sign Out Admin
      </a>
    </div>
  </aside>

  <!-- Main Content Area -->
  <main class="admin-main">
    <?php if ($flash): ?>
      <div class="flash-alert <?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom: 20px;">
        <span><?php echo htmlspecialchars($flash['message']); ?></span>
      </div>
    <?php endif; ?>
