<?php
/**
 * Valenti Atelier - Secure Multi-Method Checkout Flow (Light Mode & Responsive)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();
$cartData = calculateCartTotals($pdo);

if (empty($cartData['items'])) {
    setFlash('info', 'Your shopping bag is empty. Please add pieces before checking out.');
    redirect('/shop.php');
}

$user = currentUser();
$userProfile = null;
if ($user) {
    $uStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $uStmt->execute([$user['id']]);
    $userProfile = $uStmt->fetch();
}

// Process Checkout Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
    $customerName = sanitize($_POST['customer_name'] ?? '');
    $customerEmail = filter_var($_POST['customer_email'] ?? '', FILTER_VALIDATE_EMAIL);
    $customerPhone = sanitize($_POST['customer_phone'] ?? '');
    $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
    $shippingCity = sanitize($_POST['shipping_city'] ?? '');
    $shippingPostal = sanitize($_POST['shipping_postal'] ?? '');
    $shippingCountry = sanitize($_POST['shipping_country'] ?? 'United States');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'Credit/Debit Card');
    $orderNotes = sanitize($_POST['order_notes'] ?? '');

    if (!$customerName || !$customerEmail || !$shippingAddress || !$shippingCity || !$shippingPostal) {
        setFlash('error', 'Please fill in all mandatory contact and shipping fields.');
    } else {
        try {
            $pdo->beginTransaction();

            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), 6));

            $insOrder = $pdo->prepare("
                INSERT INTO orders (
                    order_number, user_id, customer_name, customer_email, customer_phone,
                    shipping_address, shipping_city, shipping_postal, shipping_country,
                    subtotal, discount_amount, coupon_code, shipping_fee, tax_amount, total_amount,
                    payment_method, payment_status, order_status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Paid', 'Processing', ?)
            ");

            $insOrder->execute([
                $orderNumber,
                $user ? $user['id'] : NULL,
                $customerName,
                $customerEmail,
                $customerPhone,
                $shippingAddress,
                $shippingCity,
                $shippingPostal,
                $shippingCountry,
                $cartData['subtotal'],
                $cartData['discount'],
                $cartData['coupon_code'],
                $cartData['shipping'],
                $cartData['tax'],
                $cartData['total'],
                $paymentMethod,
                $orderNotes
            ]);

            $orderId = $pdo->lastInsertId();

            // Insert line items & update stock
            $insItem = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, price, size, color, quantity, total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $updStock = $pdo->prepare("UPDATE products SET stock = CASE WHEN stock >= ? THEN stock - ? ELSE 0 END WHERE id = ?");

            foreach ($cartData['items'] as $item) {
                $insItem->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['price'],
                    $item['size'],
                    $item['color'],
                    $item['quantity'],
                    $item['total']
                ]);

                $updStock->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
            }

            $pdo->commit();

            // Clear cart
            clearCart();

            setFlash('success', 'Your order has been confirmed successfully!');
            redirect('/order_confirmation.php?order=' . urlencode($orderNumber));

        } catch (Exception $e) {
            $pdo->rollBack();
            setFlash('error', 'Checkout error: ' . $e->getMessage());
        }
    }
}

$pageTitle = "Atelier Checkout";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 36px; padding-bottom: 80px;">
  
  <div style="margin-bottom: 24px;">
    <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent-gold); margin-bottom: 6px; font-weight: 600;">
      <a href="/cart.php" style="color: var(--text-muted);">Shopping Bag</a> / <span>Checkout</span>
    </div>
    <h1 style="font-size: clamp(1.8rem, 3vw, 2.2rem); color: var(--text-primary);">Atelier Checkout</h1>
  </div>

  <form action="/checkout.php" method="POST">
    <input type="hidden" name="action" value="place_order">

    <div class="cart-grid">
      
      <!-- Shipping & Payment Form Box -->
      <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Contact & Delivery Address -->
        <div class="cart-table-card">
          <h3 style="font-size: 1.2rem; color: var(--text-primary); margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
            <span style="width: 26px; height: 26px; border-radius: 50%; background: var(--accent-gold); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">1</span>
            Shipping &amp; Client Coordinates
          </h3>

          <?php if (!$user): ?>
            <div style="background: var(--bg-subtle); border: 1px solid var(--border-medium); border-radius: var(--radius-sm); padding: 12px 14px; font-size: 0.85rem; margin-bottom: 18px;">
              Already a patron? <a href="/login.php" style="color: var(--accent-gold); font-weight: 700; text-decoration: underline;">Sign In</a> to auto-fill your saved shipping details.
            </div>
          <?php endif; ?>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Full Name *</label>
              <input type="text" name="customer_name" required value="<?php echo htmlspecialchars($userProfile['name'] ?? ''); ?>" placeholder="e.g. Aisha Abdulsalam" class="form-control">
            </div>
            <div class="form-group">
              <label class="form-label">Email Address *</label>
              <input type="email" name="customer_email" required value="<?php echo htmlspecialchars($userProfile['email'] ?? ''); ?>" placeholder="e.g. sulaiman@example.com" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Telephone Number *</label>
              <input type="tel" name="customer_phone" required value="<?php echo htmlspecialchars($userProfile['phone'] ?? '+1 (555) 987-6543'); ?>" placeholder="+1 (555) 000-0000" class="form-control">
            </div>
            <div class="form-group">
              <label class="form-label">Country / Region *</label>
              <select name="shipping_country" class="form-control">
                <option value="United States">United States</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="Canada">Canada</option>
                <option value="Nigeria">Nigeria</option>
                <option value="France">France</option>
                <option value="Germany">Germany</option>
                <option value="United Arab Emirates">United Arab Emirates</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Street Address &amp; Suite / Unit *</label>
            <input type="text" name="shipping_address" required value="<?php echo htmlspecialchars($userProfile['address'] ?? '14 Kensington High Boulevard'); ?>" placeholder="Street address, apartment, suite" class="form-control">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Town / City *</label>
              <input type="text" name="shipping_city" required value="<?php echo htmlspecialchars($userProfile['city'] ?? 'Boston'); ?>" placeholder="City" class="form-control">
            </div>
            <div class="form-group">
              <label class="form-label">Postal / ZIP Code *</label>
              <input type="text" name="shipping_postal" required value="<?php echo htmlspecialchars($userProfile['postal_code'] ?? '02115'); ?>" placeholder="Postal code" class="form-control">
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Special Delivery / Concierge Instructions</label>
            <textarea name="order_notes" rows="2" placeholder="e.g. Leave with building concierge, ring bell #4..." class="form-control"></textarea>
          </div>
        </div>

        <!-- Payment Method Selection -->
        <div class="cart-table-card">
          <h3 style="font-size: 1.2rem; color: var(--text-primary); margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
            <span style="width: 26px; height: 26px; border-radius: 50%; background: var(--accent-gold); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">2</span>
            Payment Simulation Method
          </h3>

          <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
            
            <label style="display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); cursor: pointer; background: #ffffff;">
              <input type="radio" name="payment_method" value="Credit/Debit Card" checked style="margin-top: 4px; accent-color: var(--accent-gold);">
              <div>
                <strong style="color: var(--text-primary); display: block; font-size: 0.92rem;">Credit / Debit Card (Visa, Mastercard, Amex)</strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Simulated instant checkout verification with tokenized processing.</span>
              </div>
            </label>

            <label style="display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); cursor: pointer; background: #ffffff;">
              <input type="radio" name="payment_method" value="Mobile Money / Instant Transfer" style="margin-top: 4px; accent-color: var(--accent-gold);">
              <div>
                <strong style="color: var(--text-primary); display: block; font-size: 0.92rem;">Mobile Money / Electronic Bank Transfer</strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Direct digital payment via mobile banking API or gateway.</span>
              </div>
            </label>

            <label style="display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); cursor: pointer; background: #ffffff;">
              <input type="radio" name="payment_method" value="Cash on Delivery" style="margin-top: 4px; accent-color: var(--accent-gold);">
              <div>
                <strong style="color: var(--text-primary); display: block; font-size: 0.92rem;">Cash on Delivery / White-Glove Courier</strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Settle upon arrival and garment inspection.</span>
              </div>
            </label>

          </div>

          <!-- Card Simulation Fields -->
          <div id="cardSimFields" style="background: var(--bg-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 16px;">
            <div class="form-group">
              <label class="form-label">Card Number (Simulated Demo)</label>
              <input type="text" value="4242 •••• •••• 4242" readonly class="form-control" style="letter-spacing: 0.12em; font-family: monospace; background: #ffffff;">
            </div>
            <div class="form-row">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Expiry Date</label>
                <input type="text" value="12 / 28" readonly class="form-control" style="background: #ffffff;">
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">CVV / CVC</label>
                <input type="text" value="888" readonly class="form-control" style="background: #ffffff;">
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Right Column: Order Confirmation Summary -->
      <div class="cart-summary-card" style="position: sticky; top: 100px;">
        <h3 style="font-size: 1.25rem; color: var(--text-primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 14px;">Order Summary (<?php echo count($cartData['items']); ?> items)</h3>

        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 18px; max-height: 240px; overflow-y: auto; padding-right: 4px;">
          <?php foreach ($cartData['items'] as $it): ?>
            <div style="display: flex; gap: 12px; align-items: center; font-size: 0.85rem;">
              <img src="/<?php echo htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['name']); ?>" style="width: 44px; height: 55px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-subtle);">
              <div style="flex: 1;">
                <div style="color: var(--text-primary); font-weight: 700; line-height: 1.3;"><?php echo htmlspecialchars($it['name']); ?></div>
                <div style="color: var(--text-muted); font-size: 0.75rem;">Qty: <?php echo $it['quantity']; ?> &bull; <?php echo htmlspecialchars($it['size']); ?></div>
              </div>
              <div style="font-weight: 700; color: var(--accent-gold);"><?php echo formatPrice($it['total']); ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="summary-row">
          <span>Subtotal</span>
          <span><?php echo formatPrice($cartData['subtotal']); ?></span>
        </div>

        <?php if ($cartData['discount'] > 0): ?>
          <div class="summary-row" style="color: var(--success); font-weight: 600;">
            <span>Voucher (<?php echo htmlspecialchars($cartData['coupon_code']); ?>)</span>
            <span>&minus;<?php echo formatPrice($cartData['discount']); ?></span>
          </div>
        <?php endif; ?>

        <div class="summary-row">
          <span>Shipping</span>
          <span><?php echo ($cartData['shipping'] == 0.0) ? '<span style="color:var(--success);font-weight:700;">COMPLIMENTARY</span>' : formatPrice($cartData['shipping']); ?></span>
        </div>

        <div class="summary-row">
          <span>Tax (7.5%)</span>
          <span><?php echo formatPrice($cartData['tax']); ?></span>
        </div>

        <div class="summary-row summary-total">
          <span>Total To Settle</span>
          <span style="color: var(--accent-gold);"><?php echo formatPrice($cartData['total']); ?></span>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="height: 50px; margin-top: 22px;">
          Authorize &amp; Complete Order
        </button>

        <div style="margin-top: 14px; text-align: center; font-size: 0.75rem; color: var(--text-muted);">
          By placing your order, you agree to the Valenti Atelier Terms of Purchase &amp; SEN 803 Demonstration Policy.
        </div>
      </div>

    </div>
  </form>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
