/**
 * Valenti Atelier - Main Client Interactions (Light Mode & Responsive)
 */

document.addEventListener('DOMContentLoaded', () => {
  // Mobile Navigation Drawer Toggle
  const mobileToggle = document.getElementById('mobileNavToggle');
  const mobileDrawer = document.getElementById('mobileNavDrawer');

  if (mobileToggle && mobileDrawer) {
    mobileToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      mobileDrawer.classList.toggle('open');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      if (!mobileDrawer.contains(e.target) && !mobileToggle.contains(e.target)) {
        mobileDrawer.classList.remove('open');
      }
    });
  }

  // Quantity Selector Controls
  document.querySelectorAll('.qty-control').forEach(ctrl => {
    const decBtn = ctrl.querySelector('.qty-dec');
    const incBtn = ctrl.querySelector('.qty-inc');
    const input = ctrl.querySelector('.qty-input');

    if (decBtn && incBtn && input) {
      decBtn.addEventListener('click', (e) => {
        e.preventDefault();
        let val = parseInt(input.value) || 1;
        if (val > 1) {
          input.value = val - 1;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });

      incBtn.addEventListener('click', (e) => {
        e.preventDefault();
        let val = parseInt(input.value) || 1;
        const max = parseInt(input.getAttribute('max')) || 99;
        if (val < max) {
          input.value = val + 1;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    }
  });

  // Auto-submit cart form on quantity change
  document.querySelectorAll('.cart-qty-form .qty-input').forEach(input => {
    input.addEventListener('change', () => {
      input.closest('form').submit();
    });
  });

  // Size option pill click enhancement
  document.querySelectorAll('.size-pill').forEach(pill => {
    pill.addEventListener('click', () => {
      const radio = pill.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
      }
    });
  });

  // Color option pill click enhancement
  document.querySelectorAll('.color-pill').forEach(pill => {
    pill.addEventListener('click', () => {
      const radio = pill.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
      }
    });
  });

  // Auto-dismiss Flash Alerts
  const alerts = document.querySelectorAll('.flash-alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
});
