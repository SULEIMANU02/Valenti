<?php
/**
 * Valenti Atelier - Footer Component
 */
require_once dirname(__DIR__) . '/config/config.php';
?>
  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="container">
      <div class="footer-grid">
        
        <!-- Brand Ethos -->
        <div>
          <h4 class="brand-title" style="font-size: 1.3rem; margin-bottom: 12px;"><?php echo BRAND_NAME; ?></h4>
          <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.7; margin-bottom: 20px;">
            A modern study in structural tailoring and effortless drape. Engineered with premium virgin wools, pure silks, and heavy organic cottons. Designed for the discerning individual.
          </p>
          <div style="font-size: 0.78rem; color: var(--accent-gold); letter-spacing: 0.1em;">
            SEN 803 &bull; SOFTWARE TECHNOLOGY COURSE PROJECT
          </div>
        </div>

        <!-- Navigation Links -->
        <div>
          <div class="footer-col-title">Collections</div>
          <ul class="footer-links">
            <li><a href="/shop.php?category=outerwear">Outerwear & Coats</a></li>
            <li><a href="/shop.php?category=tailoring">Tailoring & Suiting</a></li>
            <li><a href="/shop.php?category=knitwear-tops">Cashmere & Knitwear</a></li>
            <li><a href="/shop.php?category=trousers">Trousers & Bottoms</a></li>
            <li><a href="/shop.php?category=dresses-silk">Silks & Evening</a></li>
          </ul>
        </div>

        <!-- Customer Care & Portal -->
        <div>
          <div class="footer-col-title">Client Concierge</div>
          <ul class="footer-links">
            <li><a href="/account.php">Track Order & History</a></li>
            <li><a href="/cart.php">Shopping Bag</a></li>
            <li><a href="/login.php">Client Sign In</a></li>
            <li><a href="/register.php">Create Account</a></li>
            <li><a href="/admin/index.php" style="color: var(--accent-gold);">Admin Management Console</a></li>
          </ul>
        </div>

        <!-- Newsletter & Promotions -->
        <div>
          <div class="footer-col-title">Private Atelier Club</div>
          <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 14px;">
            Subscribe to receive private showroom previews, seasonal lookbooks, and 10% off your inaugural order with code <strong>WELCOME10</strong>.
          </p>
          <form action="/index.php" method="POST" style="display: flex; gap: 8px;">
            <input type="email" name="newsletter_email" required placeholder="Enter email address..." class="coupon-input" style="padding: 10px 14px;">
            <button type="submit" name="action" value="subscribe_newsletter" class="btn btn-primary btn-sm">Join</button>
          </form>
        </div>

      </div>

      <!-- Footer Bottom Bar -->
      <div class="footer-bottom">
        <div>
          &copy; <?php echo date('Y'); ?> <?php echo BRAND_NAME; ?>. All rights reserved. Built for SEN 803 Course Assessment.
        </div>
        <div style="display: flex; gap: 20px;">
          <span>Demonstration Environment</span>
          <span>&bull;</span>
          <span>B2C E-Commerce Prototype</span>
        </div>
      </div>
    </div>
  </footer>

  <script src="/assets/js/main.js"></script>
</body>
</html>
