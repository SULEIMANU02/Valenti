<?php
/**
 * Valenti Atelier - Customer Registration (Light Mode & Responsive)
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
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $postal = sanitize($_POST['postal_code'] ?? '');

    if (!$name || !$email || strlen($password) < 6) {
        setFlash('error', 'Please provide a valid name, email, and password of at least 6 characters.');
    } else {
        // Check for duplicate
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            setFlash('error', 'An account already exists under this email address.');
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $ins = $pdo->prepare("
                INSERT INTO users (name, email, password, role, phone, address, city, postal_code) 
                VALUES (?, ?, ?, 'customer', ?, ?, ?, ?)
            ");
            $ins->execute([$name, $email, $hashed, $phone, $address, $city, $postal]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'customer';

            setFlash('success', 'Your Atelier profile has been created successfully. Enjoy 10% off with coupon WELCOME10!');
            redirect('/account.php');
        }
    }
}

$pageTitle = "Create Patron Profile";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 50px 20px; max-width: 560px;">
  
  <div style="background: var(--bg-surface); border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 36px 30px; box-shadow: var(--shadow-md);">
    
    <div style="text-align: center; margin-bottom: 26px;">
      <span style="font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-gold); display: block; margin-bottom: 6px; font-weight: 700;">
        Valenti Atelier
      </span>
      <h1 style="font-size: 1.8rem; color: var(--text-primary);">Register Patron Profile</h1>
      <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">Join the private circle to unlock personalized fittings and order tracking.</p>
    </div>

    <form action="/register.php" method="POST">
      <div class="form-group">
        <label class="form-label">Full Legal Name *</label>
        <input type="text" name="name" required placeholder="e.g. Tariq Al-Mansoor" class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">Email Address *</label>
        <input type="email" name="email" required placeholder="name@domain.com" class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">Password (Minimum 6 characters) *</label>
        <input type="password" name="password" required minlength="6" placeholder="Create secure password" class="form-control">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="tel" name="phone" placeholder="+1 (555) 000-0000" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="city" placeholder="e.g. New York" class="form-control">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Street Address</label>
          <input type="text" name="address" placeholder="e.g. 120 Mercer St" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Postal Code</label>
          <input type="text" name="postal_code" placeholder="10012" class="form-control">
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="height: 48px; margin-top: 18px;">
        Create Account &rarr;
      </button>
    </form>

    <div style="margin-top: 22px; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
      Already registered? <a href="/login.php" style="color: var(--accent-gold); text-decoration: underline; font-weight: 600;">Sign In Here</a>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
