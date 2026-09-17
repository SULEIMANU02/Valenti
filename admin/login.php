<?php
/**
 * Valenti Atelier - Admin Dedicated Login (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if (isAdmin()) {
    redirect('/admin/index.php');
}

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = 'admin';

        setFlash('success', "Welcome to the Operations Console, {$user['name']}.");
        redirect('/admin/index.php');
    } else {
        setFlash('error', 'Unauthorized administrative credentials.');
    }
}

$pageTitle = "Operations Console Sign In";
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container" style="padding: 60px 20px; max-width: 460px;">
  
  <div style="background: var(--bg-surface); border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 36px 30px; box-shadow: var(--shadow-lg);">
    
    <div style="text-align: center; margin-bottom: 24px;">
      <span style="font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-gold); display: block; margin-bottom: 6px; font-weight: 700;">
        Restricted Access &bull; SEN 803
      </span>
      <h1 style="font-size: 1.8rem; color: var(--text-primary);">Admin Console Sign In</h1>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">Atelier operations, product inventory, and order fulfillment.</p>
    </div>

    <div style="background: var(--bg-subtle); border: 1px solid var(--border-medium); border-radius: var(--radius-sm); padding: 14px 16px; font-size: 0.82rem; margin-bottom: 20px;">
      <strong style="color: var(--accent-gold); display: block; margin-bottom: 4px;">Default Admin Credentials:</strong>
      <div style="color: var(--text-secondary); margin-bottom: 2px;">Email: <code style="color: var(--text-primary); font-weight: 700; background: #fff; padding: 2px 6px; border-radius: 3px; border: 1px solid var(--border-subtle);">admin@valenti.com</code></div>
      <div style="color: var(--text-secondary);">Password: <code style="color: var(--text-primary); font-weight: 700; background: #fff; padding: 2px 6px; border-radius: 3px; border: 1px solid var(--border-subtle);">admin123</code></div>
    </div>

    <form action="/admin/login.php" method="POST">
      <div class="form-group">
        <label class="form-label">Administrator Email</label>
        <input type="email" name="email" required value="admin@valenti.com" class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">Security Password</label>
        <input type="password" name="password" required value="admin123" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="height: 48px; margin-top: 20px;">
        Access Console &rarr;
      </button>
    </form>

    <div style="margin-top: 22px; text-align: center; font-size: 0.82rem;">
      <a href="/index.php" style="color: var(--text-muted);">&larr; Return to Public Storefront</a>
    </div>

  </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
