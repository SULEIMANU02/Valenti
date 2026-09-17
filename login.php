<?php
/**
 * Valenti Atelier - Customer Login (Light Mode & Responsive)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();

if (isLoggedIn()) {
    redirect('/account.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            setFlash('success', "Welcome back, {$user['name']}!");
            
            $target = $_SESSION['redirect_after_login'] ?? ($user['role'] === 'admin' ? '/admin/index.php' : '/account.php');
            unset($_SESSION['redirect_after_login']);
            redirect($target);
        } else {
            setFlash('error', 'Invalid email address or password combination.');
        }
    } else {
        setFlash('error', 'Please complete both email and password.');
    }
}

$pageTitle = "Client Sign In";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 60px 20px; max-width: 480px;">
  
  <div style="background: var(--bg-surface); border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 36px 30px; box-shadow: var(--shadow-md);">
    
    <div style="text-align: center; margin-bottom: 26px;">
      <span style="font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-gold); display: block; margin-bottom: 6px; font-weight: 700;">
        Valenti Atelier Client Portal
      </span>
      <h1 style="font-size: 1.8rem; color: var(--text-primary);">Sign In to Your Account</h1>
      <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">Manage your wardrobe orders and personal fitting specs.</p>
    </div>

    <!-- Demo Credentials Helper Box -->
    <div style="background: var(--bg-subtle); border: 1px solid var(--border-medium); border-radius: var(--radius-sm); padding: 14px 16px; font-size: 0.82rem; margin-bottom: 22px;">
      <strong style="color: var(--accent-gold); display: block; margin-bottom: 4px;">SEN 803 Demo Test Accounts:</strong>
      <div style="color: var(--text-secondary); margin-bottom: 3px;">
        Patron: <code style="color: var(--text-primary); font-weight: 700; background: #fff; padding: 2px 6px; border-radius: 3px; border: 1px solid var(--border-subtle);">customer@valenti.com</code> / <code style="color: var(--text-primary); font-weight: 700; background: #fff; padding: 2px 6px; border-radius: 3px; border: 1px solid var(--border-subtle);">customer123</code>
      </div>
      <div style="color: var(--text-secondary);">
        Admin: <code style="color: var(--text-primary); font-weight: 700; background: #fff; padding: 2px 6px; border-radius: 3px; border: 1px solid var(--border-subtle);">admin@valenti.com</code> / <code style="color: var(--text-primary); font-weight: 700; background: #fff; padding: 2px 6px; border-radius: 3px; border: 1px solid var(--border-subtle);">admin123</code>
      </div>
    </div>

    <form action="/login.php" method="POST">
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" required placeholder="name@domain.com" class="form-control" value="customer@valenti.com">
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" required placeholder="Enter password" class="form-control" value="customer123">
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="height: 48px; margin-top: 22px;">
        Sign In &rarr;
      </button>
    </form>

    <div style="margin-top: 22px; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
      Don't have an account? <a href="/register.php" style="color: var(--accent-gold); text-decoration: underline; font-weight: 600;">Create Patron Profile</a>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
